<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceCategorySeeder::class,
            ServicePackageSeeder::class,
            PackageAddonSeeder::class,
            OperatingHourSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}