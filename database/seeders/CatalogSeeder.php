<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Idempotent catalog bootstrap for deploys (e.g. Render).
 * Always refreshes categories and demo products/images.
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
