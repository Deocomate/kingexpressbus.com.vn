<?php

namespace App\Support\Client;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SearchDataBuilder
{
    public static function make(array $overrides = []): array
    {
        if (!self::isDatabaseReady()) {
            return self::minimalResponse($overrides);
        }

        $locations = collect();

        $provinces = DB::table('provinces')
            ->select('id', 'name', 'priority')
            ->orderByDesc('priority')
            ->orderBy('name')
            ->get();

        foreach ($provinces as $province) {
            $locations->push([
                'id' => (int) $province->id,
                'name' => (string) $province->name,
                'type' => 'province',
                'type_label' => 'Tỉnh/Thành phố',
                'context' => null,
                'address' => null,
                'priority' => (int) ($province->priority ?? 0),
            ]);
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

            $locations->push([
                'id' => (int) $district->id,
                'name' => (string) $district->name,
                'type' => 'district',
                'type_label' => 'Quận/Huyện',
                'context' => $contextParts ? implode(' · ', $contextParts) : ($district->province_name ?? null),
                'address' => null,
                'priority' => (int) ($district->priority ?? 0),
            ]);
        }

        $stops = DB::table('stops as s')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select('s.id', 's.name', 's.address', 's.priority', 'd.name as district_name', 'p.name as province_name')
            ->orderByDesc('s.priority')
            ->orderBy('s.name')
            ->get();

        foreach ($stops as $stop) {
            $locations->push([
                'id' => (int) $stop->id,
                'name' => (string) $stop->name,
                'type' => 'stop',
                'type_label' => 'Điểm đón/trả',
                'context' => trim(sprintf('%s, %s', $stop->district_name, $stop->province_name), ', '),
                'address' => $stop->address,
                'priority' => (int) ($stop->priority ?? 0),
            ]);
        }

        $typeOrder = ['province' => 0, 'district' => 1, 'stop' => 2];

        $uniqueLocations = $locations
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

                return Str::lower($a['name']) <=> Str::lower($b['name']);
            })
            ->values()
            ->map(function (array $item) {
                $item['priority'] = (int) ($item['priority'] ?? 0);
                $item['context'] = $item['context'] ? (string) $item['context'] : null;
                $item['address'] = $item['address'] ? (string) $item['address'] : null;

                return $item;
            })
            ->all();

        $defaultOrigin = $provinces->first();
        $defaultDestination = $provinces->skip(1)->first() ?? $defaultOrigin;

        $defaults = [
            'origin' => $defaultOrigin ? [
                'id' => (int) $defaultOrigin->id,
                'type' => 'province',
                'name' => (string) $defaultOrigin->name,
            ] : null,
            'destination' => $defaultDestination ? [
                'id' => (int) $defaultDestination->id,
                'type' => 'province',
                'name' => (string) $defaultDestination->name,
            ] : null,
            'departure_date' => now()->format('d/m/Y'),
            'return_date' => null,
        ];

        $base = [
            'locations' => $uniqueLocations,
            'defaults' => $defaults,
        ];

        if (isset($overrides['locations']) && is_array($overrides['locations'])) {
            $base['locations'] = $overrides['locations'];
        }

        if (isset($overrides['defaults']) && is_array($overrides['defaults'])) {
            $base['defaults'] = array_replace($base['defaults'], $overrides['defaults']);
        }

        $extra = Arr::except($overrides, ['locations', 'defaults']);

        return array_replace($base, $extra);
    }

    protected static function isDatabaseReady(): bool
    {
        return Schema::hasTable('provinces')
            && Schema::hasTable('districts')
            && Schema::hasTable('stops');
    }

    protected static function minimalResponse(array $overrides): array
    {
        $base = [
            'locations' => [],
            'defaults' => [
                'origin' => null,
                'destination' => null,
                'departure_date' => now()->format('d/m/Y'),
                'return_date' => null,
            ],
        ];

        if (isset($overrides['locations']) && is_array($overrides['locations'])) {
            $base['locations'] = $overrides['locations'];
        }

        if (isset($overrides['defaults']) && is_array($overrides['defaults'])) {
            $base['defaults'] = array_replace($base['defaults'], $overrides['defaults']);
        }

        return array_replace($base, Arr::except($overrides, ['locations', 'defaults']));
    }
}
