<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('is_admin', true)->value('id') ?? 1;

        $tree = [
            'Electronics' => ['Wireless Earbuds', 'Power Banks', 'Phone Cables', 'Portable Speakers'],
            'Fashion' => ["Women's Dresses", 'T-Shirts & Tanks', 'Watches'],
            'Home & Living' => ['Space Savers', 'Draperies & Curtains', 'Kitchen Essentials'],
            'Groceries' => ['Instant Noodles', 'Coffee & Creamers', 'Snacks'],
            'Health & Beauty' => ['Fragrance', 'Skincare', 'Personal Care'],
            'Mother & Baby' => ['Diapers', 'Baby Care'],
            'Sports & Outdoors' => ['Fitness', 'Outdoor Gear'],
        ];

        foreach ($tree as $parentName => $children) {
            $parent = Category::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'active' => true,
                    'parent_id' => null,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            foreach ($children as $childName) {
                Category::updateOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($childName)],
                    [
                        'name' => $childName,
                        'active' => true,
                        'parent_id' => $parent->id,
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                    ]
                );
            }
        }
    }
}
