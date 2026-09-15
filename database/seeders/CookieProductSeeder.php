<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Flavor;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductWeight;
use App\Models\Weight;
use App\Models\DeliveryOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CookieProductSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------
        // Step 1: Cookies category banao (agar nahi hai to)
        // ---------------------------------------------------------
        $cookiesCategory = Category::firstOrCreate(
            ['slug' => 'cookies'],
            ['name' => 'Cookies', 'is_active' => true]
        );

        // ---------------------------------------------------------
        // Step 2: Cookie-specific pack sizes (Weight table reuse)
        // ---------------------------------------------------------
        $packSizes = [
            ['label' => 'Pack of 6', 'value_kg' => 0.15, 'serves' => 6],
            ['label' => 'Pack of 12', 'value_kg' => 0.3, 'serves' => 12],
            ['label' => '250g Box', 'value_kg' => 0.25, 'serves' => 8],
        ];

        $weightIds = [];
        foreach ($packSizes as $i => $pack) {
            $weight = Weight::firstOrCreate(
                ['label' => $pack['label']],
                $pack + ['sort_order' => 100 + $i, 'is_active' => true]
            );
            $weightIds[$pack['label']] = $weight->id;
        }

        // ---------------------------------------------------------
        // Step 3: Cookie-specific flavors
        // ---------------------------------------------------------
        $flavorNames = ['Jeera', 'Almond', 'Oatmeal', 'Double Chocolate Chip', 'Multigrain', 'Coconut'];
        $flavorIds = [];
        foreach ($flavorNames as $name) {
            $flavor = Flavor::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true]);
            $flavorIds[$name] = $flavor->id;
        }

        $deliveryOptions = DeliveryOption::where('is_active', true)->get();

        // ---------------------------------------------------------
        // Step 4: Products — image_name yahan daalo, tumhe
        // public/userassets/products/cookies/ folder me isi naam
        // se file daalni hogi
        // ---------------------------------------------------------
        $products = [
            [
                'name' => 'Jeera Butter Cookies Jar',
                'image_name' => 'cookie-jeera-1.jpg',
                'flavor' => 'Jeera',
                'base_price' => 249,
                'description' => 'Crunchy, buttery jeera cookies baked fresh and packed in a reusable jar. Perfect with evening tea.',
            ],
            [
                'name' => 'Almond Crunch Cookies',
                'image_name' => 'cookie-almond-1.jpg',
                'flavor' => 'Almond',
                'base_price' => 299,
                'description' => 'Rich almond cookies with a delightful crunch in every bite, made with real almond pieces.',
            ],
            [
                'name' => 'Choco Chip Classic Cookies',
                'image_name' => 'cookie-chocochip-1.jpg',
                'flavor' => 'Double Chocolate Chip',
                'base_price' => 199,
                'description' => 'Classic double chocolate chip cookies — soft center, crisp edges, loaded with chocolate chunks.',
            ],
            [
                'name' => 'Healthy Oatmeal Cookies',
                'image_name' => 'cookie-oatmeal-1.jpg',
                'flavor' => 'Oatmeal',
                'base_price' => 279,
                'description' => 'Wholesome oatmeal cookies, a guilt-free treat packed with fibre and natural sweetness.',
            ],
            [
                'name' => 'Multigrain Digestive Cookies',
                'image_name' => 'cookie-multigrain-1.jpg',
                'flavor' => 'Multigrain',
                'base_price' => 229,
                'description' => 'A healthy blend of multigrain goodness, light and crispy, great for a quick snack.',
            ],
        ];

        foreach ($products as $index => $data) {
            $product = Product::updateOrCreate(
                ['sku' => 'COOKIE-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'category_id' => $cookiesCategory->id,
                    'subcategory_id' => null,
                    'child_category_id' => null,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']) . '-' . ($index + 1),
                    'short_description' => Str::limit($data['description'], 100),
                    'description' => $data['description'],
                    'base_price' => $data['base_price'],
                    'discount_price' => null,
                    'egg_type' => 'eggless',
                    'is_photo_cake' => false,
                    'is_message_enabled' => false,
                    'message_char_limit' => 30,
                    'meta_title' => $data['name'] . ' | Order Online | Sweet Bakes',
                    'meta_description' => 'Order ' . $data['name'] . ' online with fast delivery. Freshly baked cookies.',
                    'meta_keywords' => strtolower(str_replace(' ', ', ', $data['name'])) . ', cookies online, biscuits',
                    'is_featured' => $index === 0,
                    'is_bestseller' => $index < 2,
                    'status' => 'active',
                ]
            );

            // Weight variants — teeno pack sizes har cookie product ke liye
            $product->weightVariants()->delete();

            $priceMultipliers = [
                'Pack of 6' => 1,
                'Pack of 12' => 1.8,
                '250g Box' => 1.4,
            ];

            $isFirst = true;
            foreach ($priceMultipliers as $label => $multiplier) {
                ProductWeight::create([
                    'product_id' => $product->id,
                    'weight_id' => $weightIds[$label],
                    'egg_type' => 'eggless',
                    'price' => round($data['base_price'] * $multiplier, -1),
                    'discount_price' => null,
                    'stock' => 50,
                    'is_default' => $isFirst,
                    'is_active' => true,
                ]);
                $isFirst = false;
            }

            // Flavor attach
            $product->flavors()->sync([
                $flavorIds[$data['flavor']] => ['price_modifier' => 0, 'is_default' => true],
            ]);

            // Delivery options attach
            if ($deliveryOptions->count()) {
                $product->deliveryOptions()->sync($deliveryOptions->pluck('id')->toArray());
            }

            // Image attach — path sirf reference hai, file khud daalni hogi
            $imagePath = 'userassets/products/cookies/' . $data['image_name'];

            $product->images()->delete();
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        $this->command->info('5 Cookie products seeded! Images ye files honi chahiye public/userassets/products/cookies/ folder me:');
        foreach ($products as $data) {
            $this->command->line('  - ' . $data['image_name']);
        }
    }
}