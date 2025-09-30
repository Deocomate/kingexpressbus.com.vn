<?php

namespace App\View\Components\Client;

use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
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
        $locations = $searchData['locations'] ?? null;

        if (empty($locations)) {
            $locations = $this->loadLocations();
        } elseif ($locations instanceof \Illuminate\Support\Collection) {
            $locations = $locations->toArray();
        }

        $defaults = $searchData['defaults'] ?? [];
        if ($defaults instanceof \Illuminate\Support\Collection) {
            $defaults = $defaults->toArray();
        }

        $locations = is_array($locations) ? $locations : [];
        $defaults = $this->resolveDefaults($defaults, $locations);

        return [
            'locations' => $locations,
            'defaults' => $defaults,
        ];
    }

    protected function loadLocations(): array
    {
        $locations = [];

        $provinces = DB::table('provinces')
            ->select('id', 'name', 'priority')
            ->orderByDesc('priority')
            ->orderBy('name')
            ->get();

        foreach ($provinces as $province) {
            $locations[] = [
                'id' => (int) $province->id,
                'name' => (string) $province->name,
                'type' => 'province',
                'type_label' => 'Tỉnh/Thành phố',
                'context' => null,
                'address' => null,
                'priority' => (int) ($province->priority ?? 0),
            ];
        }

        $districts = DB::table('districts as d')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->leftJoin('district_types as dt', 'd.district_type_id', '=', 'dt.id')
            ->select('d.id', 'd.name', 'd.priority', 'p.name as province_name', 'dt.name as district_type_name')
            ->orderByDesc('d.priority')
            ->orderBy('d.name')
            ->get();

        foreach ($districts as $district) {
            $contextParts = array_filter([
                $district->district_type_name,
                $district->province_name,
            ]);

            $locations[] = [
                'id' => (int) $district->id,
                'name' => (string) $district->name,
                'type' => 'district',
                'type_label' => 'Quận/Huyện',
                'context' => $contextParts ? implode(' · ', $contextParts) : ($district->province_name ?? null),
                'address' => null,
                'priority' => (int) ($district->priority ?? 0),
            ];
        }

        $stops = DB::table('stops as s')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select('s.id', 's.name', 's.address', 's.priority', 'd.name as district_name', 'p.name as province_name')
            ->orderByDesc('s.priority')
            ->orderBy('s.name')
            ->get();

        foreach ($stops as $stop) {
            $locations[] = [
                'id' => (int) $stop->id,
                'name' => (string) $stop->name,
                'type' => 'stop',
                'type_label' => 'Điểm đón/trả',
                'context' => trim(sprintf('%s, %s', $stop->district_name, $stop->province_name), ', '),
                'address' => $stop->address,
                'priority' => (int) ($stop->priority ?? 0),
            ];
        }

        $typeOrder = ['province' => 0, 'district' => 1, 'stop' => 2];

        $locations = collect($locations)
            ->unique(function (array $item) {
                return $item['type'] . ':' . $item['id'];
            })
            ->sort(function (array $a, array $b) use ($typeOrder) {
                $priority = ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0);
                if ($priority !== 0) {
                    return $priority;
                }

                $typeComparison = ($typeOrder[$a['type']] ?? 99) <=> ($typeOrder[$b['type']] ?? 99);
                if ($typeComparison !== 0) {
                    return $typeComparison;
                }

                return strcasecmp($a['name'], $b['name']);
            })
            ->values()
            ->map(function (array $item) {
                $item['priority'] = (int) ($item['priority'] ?? 0);
                $item['context'] = $item['context'] ? (string) $item['context'] : null;
                $item['address'] = $item['address'] ? (string) $item['address'] : null;

                return $item;
            })
            ->all();

        return $locations;
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
            $location = $this->findLocation((int) $oldId, (string) $oldType, $locations);

            if ($location) {
                if ($oldLabel) {
                    $location['name'] = (string) $oldLabel;
                }

                return $location;
            }

            return [
                'id' => (int) $oldId,
                'type' => (string) $oldType,
                'name' => (string) ($oldLabel ?? ''),
                'type_label' => $this->typeLabel($oldType),
                'context' => null,
                'address' => null,
                'priority' => 0,
            ];
        }

        if (is_array($value) && isset($value['id'], $value['type'])) {
            $location = $this->findLocation((int) $value['id'], (string) $value['type'], $locations);

            if ($location) {
                if (!empty($value['name'])) {
                    $location['name'] = (string) $value['name'];
                }

                return $location;
            }

            return [
                'id' => (int) $value['id'],
                'type' => (string) $value['type'],
                'name' => (string) ($value['name'] ?? ''),
                'type_label' => $this->typeLabel($value['type']),
                'context' => $value['context'] ?? null,
                'address' => $value['address'] ?? null,
                'priority' => (int) ($value['priority'] ?? 0),
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
            if ((int) $location['id'] === $id && $location['type'] === $type) {
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
