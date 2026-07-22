<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('is_admin', true)->value('id') ?? 1;

        Storage::disk('public')->makeDirectory('images');

        foreach ($this->catalog() as $item) {
            $category = Category::where('slug', Str::slug($item['category']))->first();

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'compare_at_price' => $item['compare_at_price'],
                    'quantity' => $item['quantity'],
                    'published' => true,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            if ($category) {
                $product->categories()->syncWithoutDetaching([$category->id]);
            }

            $product->images()->delete();

            foreach ($item['images'] as $position => $imageUrl) {
                $stored = $this->storeImage($imageUrl, $product->id, $position);
                if ($stored) {
                    ProductImage::create($stored + [
                        'product_id' => $product->id,
                        'position' => $position,
                    ]);
                }
            }
        }
    }

    /**
     * @return list<array{
     *   title: string,
     *   category: string,
     *   price: float,
     *   compare_at_price: float|null,
     *   quantity: int,
     *   description: string,
     *   images: list<string>
     * }>
     */
    private function catalog(): array
    {
        return [
            [
                'title' => 'Nescafé Classic Instant Coffee 170g',
                'category' => 'Coffee & Creamers',
                'price' => 7.89,
                'compare_at_price' => 8.49,
                'quantity' => 180,
                'description' => $this->html(
                    'Rich and aromatic Nescafé Classic instant coffee. Perfect for everyday mornings — just add hot water for a full-bodied cup.',
                    '170g jar. Store in a cool, dry place.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Nestlé All Purpose Cream 250ml',
                'category' => 'Coffee & Creamers',
                'price' => 2.75,
                'compare_at_price' => 2.99,
                'quantity' => 240,
                'description' => $this->html(
                    'Smooth Nestlé all-purpose cream for cooking, baking, and coffee. Creamy texture that blends easily into sauces and desserts.',
                    '250ml pack. Keep refrigerated after opening.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1628088062854-d1870b455688?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Lucky Me! Pancit Canton Chilimansi 6-Pack',
                'category' => 'Instant Noodles',
                'price' => 1.85,
                'compare_at_price' => 1.99,
                'quantity' => 320,
                'description' => $this->html(
                    'Iconic Filipino stir-fry noodles with tangy chilimansi seasoning. Quick lunch or late-night snack ready in minutes.',
                    '80g × 6 multipack.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Smartfood White Cheddar Popcorn 155g',
                'category' => 'Snacks',
                'price' => 3.49,
                'compare_at_price' => 3.99,
                'quantity' => 95,
                'description' => $this->html(
                    'Crispy popcorn coated in real white cheddar. Movie-night ready with a bold, cheesy crunch.',
                    '5.5 oz (155.9g) bag.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Century Tuna Flakes in Oil 180g — Pack of 3',
                'category' => 'Snacks',
                'price' => 4.29,
                'compare_at_price' => 4.55,
                'quantity' => 150,
                'description' => $this->html(
                    'Protein-packed tuna flakes in oil. Omega-3 DHA, no preservatives — great for sandwiches, salads, and rice meals.',
                    '180g × 3 cans.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'SoundPulse Wireless Earbuds Pro',
                'category' => 'Wireless Earbuds',
                'price' => 34.99,
                'compare_at_price' => 49.99,
                'quantity' => 75,
                'description' => $this->html(
                    'True wireless earbuds with deep bass, touch controls, and a compact charging case. Up to 24 hours total playtime with the case.',
                    'Bluetooth 5.3 · IPX5 splash resistant · Includes USB-C cable.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'ChargeMax 20000mAh Power Bank',
                'category' => 'Power Banks',
                'price' => 22.50,
                'compare_at_price' => 29.99,
                'quantity' => 110,
                'description' => $this->html(
                    'High-capacity power bank with dual USB output and USB-C input. Charge two devices at once — phone, earbuds, or tablet.',
                    '20000mAh · LED battery indicator · Includes carry cable.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'FlexLink Braided USB-C Cable 2m',
                'category' => 'Phone Cables',
                'price' => 6.99,
                'compare_at_price' => 9.99,
                'quantity' => 200,
                'description' => $this->html(
                    'Durable nylon-braided USB-C cable for fast charging and data sync. Extra-long 2-meter reach for desk or bedside use.',
                    'Supports up to 60W PD · Aluminum connectors.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'BoomBox Mini Portable Bluetooth Speaker',
                'category' => 'Portable Speakers',
                'price' => 27.99,
                'compare_at_price' => 39.99,
                'quantity' => 60,
                'description' => $this->html(
                    'Pocket-friendly Bluetooth speaker with punchy sound and 12-hour battery. Pair two for stereo mode.',
                    'IPX7 waterproof · Built-in mic for calls.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'LinenEase Summer Midi Dress',
                'category' => "Women's Dresses",
                'price' => 28.00,
                'compare_at_price' => 42.00,
                'quantity' => 45,
                'description' => $this->html(
                    'Breathable linen-blend midi dress with a flattering waist tie. Ideal for warm days, brunch, or casual evenings out.',
                    'Available look: soft sage. Machine wash cold.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Everyday Cotton Crew Tee — 3 Pack',
                'category' => 'T-Shirts & Tanks',
                'price' => 18.99,
                'compare_at_price' => 24.99,
                'quantity' => 130,
                'description' => $this->html(
                    'Soft pre-shrunk cotton crew tees in a versatile 3-pack. Midweight fabric that holds shape wash after wash.',
                    'Pack: black, white, heather gray. Regular fit.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Auric Classic Analog Watch',
                'category' => 'Watches',
                'price' => 54.00,
                'compare_at_price' => 79.00,
                'quantity' => 40,
                'description' => $this->html(
                    'Minimalist analog watch with a stainless steel case and genuine leather strap. Water-resistant for daily wear.',
                    'Quartz movement · 40mm case · 2-year warranty.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'StackWell Under-Bed Storage Box Set',
                'category' => 'Space Savers',
                'price' => 19.50,
                'compare_at_price' => 27.00,
                'quantity' => 85,
                'description' => $this->html(
                    'Clear under-bed storage boxes with sturdy lids. Keep seasonal clothes, shoes, and linens organized and dust-free.',
                    'Set of 2 · Low-profile design · Locking clips.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'SoftDrape Blackout Curtain Pair',
                'category' => 'Draperies & Curtains',
                'price' => 32.99,
                'compare_at_price' => 44.99,
                'quantity' => 55,
                'description' => $this->html(
                    'Room-darkening curtains that block light and reduce noise. Grommet top for easy hanging on standard rods.',
                    'Pair · 52" × 84" · Machine washable.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'ForgeCast Nonstick Frying Pan 28cm',
                'category' => 'Kitchen Essentials',
                'price' => 24.99,
                'compare_at_price' => 34.99,
                'quantity' => 70,
                'description' => $this->html(
                    'Even-heat nonstick frying pan with a comfortable stay-cool handle. Everyday workhorse for eggs, stir-fries, and searing.',
                    'PFOA-free coating · Oven-safe to 180°C · Induction compatible.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1584990347449-a2d4c2f1f49a?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Aficionado F35 Eau De Parfum 85ml',
                'category' => 'Fragrance',
                'price' => 18.50,
                'compare_at_price' => 25.00,
                'quantity' => 90,
                'description' => $this->html(
                    'Floral-woody eau de parfum with notes of jasmine, soft musk, and warm amber. Long-lasting for day-to-night wear.',
                    '85ml spray · For women.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1541643600914-78b084683601?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'GlowLab Vitamin C Face Serum 30ml',
                'category' => 'Skincare',
                'price' => 15.99,
                'compare_at_price' => 21.99,
                'quantity' => 100,
                'description' => $this->html(
                    'Brightening vitamin C serum that helps even skin tone and boost radiance. Lightweight, fast-absorbing formula.',
                    '30ml dropper bottle · Fragrance-free · Dermatologist tested.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1620916561605-c4e987637776?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'FreshWave Bamboo Charcoal Toothbrush 4-Pack',
                'category' => 'Personal Care',
                'price' => 5.49,
                'compare_at_price' => 7.99,
                'quantity' => 160,
                'description' => $this->html(
                    'Eco-friendly bamboo toothbrushes with soft BPA-free bristles. Compostable handles for a greener bathroom routine.',
                    'Pack of 4 · Medium soft bristles.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1607613009820-a29f7bb81c40?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Huggies Ultra Soft Diapers — Size M (42 pcs)',
                'category' => 'Diapers',
                'price' => 12.99,
                'compare_at_price' => 14.99,
                'quantity' => 120,
                'description' => $this->html(
                    'Ultra-absorbent diapers with a soft cotton-like cover. Wetness indicator helps you know when it\'s time for a change.',
                    'Size M · 42 pieces · Hypoallergenic.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'TinyBuds Gentle Baby Lotion 200ml',
                'category' => 'Baby Care',
                'price' => 6.75,
                'compare_at_price' => 8.50,
                'quantity' => 140,
                'description' => $this->html(
                    'Dermatologically tested baby lotion with aloe and shea. Keeps delicate skin moisturized without heavy fragrance.',
                    '200ml pump bottle · Tear-free formula.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1519689373023-dd07c7988603?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'FlexBand Resistance Set (5 Levels)',
                'category' => 'Fitness',
                'price' => 16.99,
                'compare_at_price' => 24.99,
                'quantity' => 80,
                'description' => $this->html(
                    'Complete resistance band set for home workouts — from light mobility to heavy strength training. Includes door anchor and carry bag.',
                    '5 bands · Handles · Ankle straps · Workout guide.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1598289431512-b97b0917affc?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'TrailLite Compact Camping Lantern',
                'category' => 'Outdoor Gear',
                'price' => 14.50,
                'compare_at_price' => 19.99,
                'quantity' => 65,
                'description' => $this->html(
                    'USB-rechargeable LED lantern with three brightness modes and a hanging hook. Perfect for camping, power outages, and patio nights.',
                    'Up to 40 hours on low · IPX4 water resistant.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Samyang Hot Chicken Ramen 5-Pack',
                'category' => 'Instant Noodles',
                'price' => 4.99,
                'compare_at_price' => 5.79,
                'quantity' => 200,
                'description' => $this->html(
                    'Fiery Korean-style instant ramen with the signature hot chicken flavor. Adjust the spice by how much sauce you add.',
                    '140g × 5 packs.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1612929636598-2802bbc27bdc?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'PixelGuard Tempered Glass Screen Protector',
                'category' => 'Phone Cables',
                'price' => 4.49,
                'compare_at_price' => 7.99,
                'quantity' => 250,
                'description' => $this->html(
                    '9H tempered glass screen protector with oleophobic coating. Crystal-clear clarity and easy bubble-free installation.',
                    'Universal fit kit with alignment frame · Includes cleaning cloth.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'CloudKnit Oversized Lounge Hoodie',
                'category' => 'T-Shirts & Tanks',
                'price' => 29.99,
                'compare_at_price' => 45.00,
                'quantity' => 50,
                'description' => $this->html(
                    'Ultra-soft oversized hoodie with a brushed interior. Your go-to for travel days, study sessions, and weekend lounging.',
                    'Kangaroo pocket · Ribbed cuffs · Machine washable.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1578587018452-892b722ab364?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'BrewMate Manual Coffee Grinder',
                'category' => 'Kitchen Essentials',
                'price' => 21.00,
                'compare_at_price' => 29.00,
                'quantity' => 48,
                'description' => $this->html(
                    'Ceramic burr hand grinder with adjustable grind settings — espresso to French press. Compact for home or travel.',
                    'Stainless body · Includes brush and pouch.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'Lumina Soft Matte Lipstick — Rosewood',
                'category' => 'Skincare',
                'price' => 9.99,
                'compare_at_price' => 13.99,
                'quantity' => 110,
                'description' => $this->html(
                    'Long-wear soft matte lipstick in a flattering rosewood shade. Creamy application that won\'t dry out your lips.',
                    '3.5g bullet · Vitamin E enriched.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'AquaSip Insulated Bottle 750ml',
                'category' => 'Outdoor Gear',
                'price' => 17.99,
                'compare_at_price' => 24.99,
                'quantity' => 95,
                'description' => $this->html(
                    'Double-wall vacuum bottle that keeps drinks cold for 24 hours or hot for 12. Leak-proof lid for gym bags and desk days.',
                    '750ml · BPA-free · Wide mouth for ice cubes.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'NightLite LED Desk Lamp with USB Port',
                'category' => 'Space Savers',
                'price' => 23.50,
                'compare_at_price' => 32.00,
                'quantity' => 58,
                'description' => $this->html(
                    'Adjustable LED desk lamp with three color temperatures and a built-in USB charging port. Eye-friendly light for late work sessions.',
                    'Touch dimmer · Foldable arm · Energy efficient.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=800&h=800&fit=crop',
                ],
            ],
            [
                'title' => 'PureSilk Charcoal Face Mask 10 Sheets',
                'category' => 'Skincare',
                'price' => 8.99,
                'compare_at_price' => 12.50,
                'quantity' => 175,
                'description' => $this->html(
                    'Detoxifying charcoal sheet masks that help clear pores and refresh tired skin. Soft silk-like fabric for comfortable wear.',
                    '10 individually wrapped sheets · 20-minute treatment.'
                ),
                'images' => [
                    'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=800&h=800&fit=crop',
                ],
            ],
        ];
    }

    private function html(string $lead, string $detail): string
    {
        return '<p>' . e($lead) . '</p><p>' . e($detail) . '</p>';
    }

    /**
     * @return array{path: string, url: string, mime: string, size: int}|null
     */
    private function storeImage(string $url, int $productId, int $position): ?array
    {
        $folder = 'images/' . Str::random(16);
        $filename = Str::random(16) . '.jpg';
        $path = $folder . '/' . $filename;

        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'ShoparooProductSeeder/1.0'])
                ->get($url);

            if ($response->successful() && strlen($response->body()) > 500) {
                Storage::disk('public')->put($path, $response->body());

                return [
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                    'mime' => 'image/jpeg',
                    'size' => Storage::disk('public')->size($path),
                ];
            }
        } catch (\Throwable $e) {
            $this->command?->warn("Could not download image for product #{$productId}: {$e->getMessage()}");
        }

        return $this->storePlaceholder($path, $productId, $position);
    }

    /**
     * @return array{path: string, url: string, mime: string, size: int}|null
     */
    private function storePlaceholder(string $path, int $productId, int $position): ?array
    {
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#1e293b"/>
      <stop offset="100%" stop-color="#f97316"/>
    </linearGradient>
  </defs>
  <rect width="800" height="800" fill="url(#g)"/>
  <text x="400" y="390" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="36" font-weight="700">Shoparoo</text>
  <text x="400" y="440" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-family="Arial, sans-serif" font-size="22">Product #{$productId}-{$position}</text>
</svg>
SVG;

        $pngPath = preg_replace('/\.jpg$/', '.svg', $path);
        Storage::disk('public')->put($pngPath, $svg);

        return [
            'path' => $pngPath,
            'url' => Storage::disk('public')->url($pngPath),
            'mime' => 'image/svg+xml',
            'size' => Storage::disk('public')->size($pngPath),
        ];
    }
}
