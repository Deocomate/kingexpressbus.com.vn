<?php

namespace App\View\Components\Client;

use App\Support\Client\SearchDataBuilder;
use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchBar extends Component
{
    public array $searchData;

    public string $action;

    public string $submitLabel;

    public function __construct(array $searchData = [], ?string $action = null, string $submitLabel = 'Tìm kiếm')
    {
        $this->action = $action ?? route('client.routes.search');
        $this->submitLabel = $submitLabel;
        $this->searchData = $this->prepareSearchData($searchData);
    }

    protected function prepareSearchData(array $searchData): array
    {
        // Sử dụng SearchDataBuilder làm nguồn dữ liệu duy nhất
        // Nó đã bao gồm logic sắp xếp priority từ cao đến thấp
        $builderResult = SearchDataBuilder::make($searchData);

        // Logic cũ để phân giải giá trị mặc định vẫn được giữ lại và áp dụng
        $defaults = $this->resolveDefaults($builderResult['defaults'], $builderResult['locations']);

        return [
            'locations' => $builderResult['locations'],
            'defaults' => $defaults,
        ];
    }

    protected function resolveDefaults(array $defaults, array $locations): array
    {
        $resolved = [
            'origin' => $defaults['origin'] ?? null,
            'destination' => $defaults['destination'] ?? null,
            'departure_date' => $defaults['departure_date'] ?? null,
            'return_date' => $defaults['return_date'] ?? null,
        ];

        $resolved['origin'] = $this->resolveDefaultLocation('origin', $resolved['origin'], $locations);
        $resolved['destination'] = $this->resolveDefaultLocation('destination', $resolved['destination'], $locations, $resolved['origin']);

        $resolved['departure_date'] = $this->resolveDefaultDate('departure_date', $resolved['departure_date'], true);
        $resolved['return_date'] = $this->resolveDefaultDate('return_date', $resolved['return_date'], false);

        return $resolved;
    }

    protected function resolveDefaultLocation(string $field, $value, array $locations, $exclude = null): ?array
    {
        $oldId = old($field . '_id');
        $oldType = old($field . '_type');
        $oldLabel = old($field . '_label');

        if ($oldId && $oldType) {
            $location = $this->findLocation((int)$oldId, (string)$oldType, $locations);

            if ($location) {
                if ($oldLabel) {
                    $location['name'] = (string)$oldLabel;
                }

                return $location;
            }

            return [
                'id' => (int)$oldId,
                'type' => (string)$oldType,
                'name' => (string)($oldLabel ?? ''),
                'type_label' => $this->typeLabel($oldType),
                'context' => null,
                'address' => null,
                'priority' => 0,
            ];
        }

        if (is_array($value) && isset($value['id'], $value['type'])) {
            $location = $this->findLocation((int)$value['id'], (string)$value['type'], $locations);

            if ($location) {
                if (!empty($value['name'])) {
                    $location['name'] = (string)$value['name'];
                }

                return $location;
            }

            return [
                'id' => (int)$value['id'],
                'type' => (string)$value['type'],
                'name' => (string)($value['name'] ?? ''),
                'type_label' => $this->typeLabel($value['type']),
                'context' => $value['context'] ?? null,
                'address' => $value['address'] ?? null,
                'priority' => (int)($value['priority'] ?? 0),
            ];
        }

        return $this->fallbackLocation($locations, $exclude);
    }

    protected function resolveDefaultDate(string $field, ?string $value, bool $requireFallback): ?string
    {
        $candidate = old($field, $value);

        if ($candidate) {
            try {
                return Carbon::createFromFormat('d/m/Y', $candidate)->format('d/m/Y');
            } catch (\Throwable $e) {
                // ignore parse issue
            }
        }

        if ($requireFallback) {
            return now()->format('d/m/Y');
        }

        return null;
    }

    protected function fallbackLocation(array $locations, $exclude = null): ?array
    {
        $excludeKey = null;

        if (is_array($exclude) && isset($exclude['id'], $exclude['type'])) {
            $excludeKey = $exclude['type'] . ':' . $exclude['id'];
        }

        $preferredOrder = ['province', 'district', 'stop'];

        foreach ($preferredOrder as $type) {
            foreach ($locations as $location) {
                $key = $location['type'] . ':' . $location['id'];

                if ($location['type'] === $type && $key !== $excludeKey) {
                    return $location;
                }
            }
        }

        foreach ($locations as $location) {
            $key = $location['type'] . ':' . $location['id'];

            if ($key !== $excludeKey) {
                return $location;
            }
        }

        return null;
    }

    protected function findLocation(int $id, string $type, array $locations): ?array
    {
        foreach ($locations as $location) {
            if ((int)$location['id'] === $id && $location['type'] === $type) {
                return $location;
            }
        }

        return null;
    }

    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'district' => 'Quận/Huyện',
            'stop' => 'Điểm đón/trả',
            default => 'Tỉnh/Thành phố',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.client.search-bar');
    }
}
