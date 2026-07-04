<?php

namespace Database\Seeders;

use App\Models\PermissionCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        foreach ([
            'Xizmat safari',
            'Shaxsiy ish',
            'Davolanish',
            'Oilaviy sabab',
        ] as $name) {
            PermissionCategory::firstOrCreate(['name' => $name]);
        }
    }
}
