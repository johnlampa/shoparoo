<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Idempotent catalog bootstrap for deploys (e.g. Render).
 * Runs categories always; seeds demo products only when the catalog is sparse.
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        if (Product::withTrashed()->count() < 10) {
            $this->call(ProductSeeder::class);
        }
    }
}
