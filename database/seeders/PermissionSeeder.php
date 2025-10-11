<?php

namespace Database\Seeders;

use App\Models\Category;
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

        $categories = [
            ["name" => "XYZ", "parent_category" => null, 'image' => 'xyz.png'],
            ["name" => "ABC", "parent_category" => null, 'image' => 'abc.png'],
            ["name" => "PQR", "parent_category" => null, 'image' => 'pqr.png'],
            ["name" => "Sub XYZ 101", "parent_category" => "p1", 'image' => 'sub_xyz101.png'],
            ["name" => "Sub XYZ 102", "parent_category" => "p1", 'image' => 'sub_xyz102.png'],
            ["name" => "Sub XYZ 1", "parent_category" => "XYZ", 'image' => 'sub_xyz1.png'],
            ["name" => "Sub XYZ 2", "parent_category" => "XYZ", 'image' => 'sub_xyz2.png'],
            ["name" => "Sub ABC 1", "parent_category" => "ABC", 'image' => 'sub_abc1.png'],
            ["name" => "Sub ABC 2", "parent_category" => "ABC", 'image' => 'sub_abc2.png'],
            ["name" => "Sub PQR 1", "parent_category" => "PQR", 'image' => 'sub_pqr1.png'],
            ["name" => "Sub PQR 2", "parent_category" => "PQR",  'image' => 'sub_pqr2.png'],
            ["name" => "Sub PQR 101", "parent_category" => "p101", 'image' => 'sub_pqr1_101.png'],
            ["name" => "Sub PQR 102", "parent_category" => "p102",  'image' => 'sub_pqr2_102.png'],
        ];

        foreach ($categories as $categorie) {
            //Check if parent category exists
            if ($categorie['parent_category']) {
                $parent = Category::where('name', $categorie['parent_category'])->first();
                if ($parent) {
                    Category::firstOrCreate([
                        'name' => $categorie['name'],
                        'parent_id' => $parent->id,
                        'image' => $categorie['image']
                    ]);
                } else {
                    //if parent category is not found create as a parent category
                    Category::firstOrCreate([
                        'name' => $categorie['parent_category'],
                        'parent_id' => null,
                        'image' => 'default.png'
                    ]);
                    $newParent = Category::where('name', $categorie['parent_category'])->first();
                    Category::firstOrCreate([
                        'name' => $categorie['name'],
                        'parent_id' => $newParent->id,
                        'image' => $categorie['image']
                    ]);
                }
            }
        }
    }
}
