<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = ['dashboard', 'category', 'products', 'customers', 'orders', 'pos', 'settings'];

        foreach ($modules as $module) {
            Permission::firstOrCreate(['name' => $module . '.view']);
            Permission::firstOrCreate(['name' => $module . '.create']);
            Permission::firstOrCreate(['name' => $module . '.edit']);
            Permission::firstOrCreate(['name' => $module . '.delete']);
        }
    }
}
