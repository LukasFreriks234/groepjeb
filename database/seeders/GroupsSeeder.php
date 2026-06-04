<?php

namespace Database\Seeders;

use App\Models\Functions;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Polluting', 'is_system' => true],
            ['name' => 'Sensitive', 'is_system' => true],
        ];

        foreach ($groups as $group) {
            Group::updateOrCreate(
                ['name' => $group['name']],
                ['is_system' => $group['is_system']]
            );
        }

        $groupIds = Group::query()
            ->whereIn('name', ['Polluting', 'Sensitive'])
            ->pluck('id', 'name');

        $functionIds = Functions::query()
            ->whereIn('name', ['Road', 'Gas Station', 'Shop', 'Hospital', 'Park'])
            ->pluck('id', 'name');

        $groupMemberships = [
            ['group_id' => $groupIds['Polluting'] ?? null, 'function_id' => $functionIds['Road'] ?? null],
            ['group_id' => $groupIds['Polluting'] ?? null, 'function_id' => $functionIds['Gas Station'] ?? null],
            ['group_id' => $groupIds['Polluting'] ?? null, 'function_id' => $functionIds['Shop'] ?? null],
            ['group_id' => $groupIds['Sensitive'] ?? null, 'function_id' => $functionIds['Hospital'] ?? null],
            ['group_id' => $groupIds['Sensitive'] ?? null, 'function_id' => $functionIds['Park'] ?? null],
        ];

        DB::table('function_group')->insertOrIgnore(
            array_values(array_filter($groupMemberships, function (array $membership) {
                return $membership['group_id'] && $membership['function_id'];
            }))
        );
    }
}