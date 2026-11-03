<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductMedia;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedSettings();
        $this->seedCategoriesAndProducts();
    }

    protected function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@teamakeem.com'],
            [
                'name' => 'TeamAkeem Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }

    protected function seedSettings(): void
    {
        $settings = [
            'business_name' => ['value' => 'TeamAkeem Ladies World', 'group' => 'branding', 'type' => 'text', 'desc' => 'Business Name'],
            'clothing_brand_name' => ['value' => 'TeamAkeem Ladies Wears', 'group' => 'branding', 'type' => 'text', 'desc' => 'Clothing Brand Name'],
            'confectionery_brand_name' => ['value' => 'TeamAkeem Beauty & Wellness Spa', 'group' => 'branding', 'type' => 'text', 'desc' => 'Spa Brand Name'],
            'tagline' => ['value' => 'Luxury Ladies Wears, Intimates & Holistic Wellness Spa', 'group' => 'branding', 'type' => 'text', 'desc' => 'Website Tagline'],
            'hero_headline' => ['value' => 'Empowering Feminine Beauty & Everyday Confidence', 'group' => 'homepage', 'type' => 'text', 'desc' => 'Homepage Hero Headline'],
            'hero_subheadline' => ['value' => 'Discover exquisite ladies intimate wear, premium nightwear and modesty collections alongside restorative treatments at TeamAkeem Ladies Beauty and Wellness Spa. Seamless WhatsApp ordering & booking.', 'group' => 'homepage', 'type' => 'textarea', 'desc' => 'Homepage Subheadline'],
            'whatsapp_number' => ['value' => '2348076187311', 'group' => 'whatsapp', 'type' => 'text', 'desc' => 'WhatsApp Checkout Business Phone Number (International Format)'],
            'currency_code' => ['value' => 'NGN', 'group' => 'general', 'type' => 'text', 'desc' => 'Currency Code'],
            'currency_symbol' => ['value' => '₦', 'group' => 'general', 'type' => 'text', 'desc' => 'Currency Symbol'],
            'contact_email' => ['value' => 'teemakeemladiesworld@gmail.com', 'group' => 'contact', 'type' => 'text', 'desc' => 'Contact Email'],
            'contact_phone' => ['value' => '+234 807 618 7311 / +234 901 654 4574', 'group' => 'contact', 'type' => 'text', 'desc' => 'Contact Phone Number'],
            'address' => ['value' => 'Kano & Lagos, Nigeria (Professional Home Service Available Nationwide)', 'group' => 'contact', 'type' => 'text', 'desc' => 'Physical Location'],
            'instagram_url' => ['value' => 'https://instagram.com/teemakeemladiesworld', 'group' => 'social', 'type' => 'text', 'desc' => 'Instagram URL'],
            'facebook_url' => ['value' => 'https://facebook.com/teemakeemladiesworld', 'group' => 'social', 'type' => 'text', 'desc' => 'Facebook URL'],
            'delivery_info' => ['value' => 'Fast nationwide delivery across Nigeria for all ladies wear & intimates. Dedicated home service available for Beauty & Wellness Spa sessions upon booking.', 'group' => 'checkout', 'type' => 'textarea', 'desc' => 'Delivery and Collection Information'],
            'logo_path' => ['value' => 'images/logo.png', 'group' => 'branding', 'type' => 'image', 'desc' => 'Light Mode Logo'],
            'logo_dark_path' => ['value' => 'images/logo_dark.png', 'group' => 'branding', 'type' => 'image', 'desc' => 'Dark Mode Logo'],
            'about_story' => ['value' => 'TeamAkeem Ladies World is a premier sanctuary dedicated exclusively to feminine grace, comfort, and holistic well-being. We bring together two essential passions under one roof: a carefully curated collection of luxury ladies wear, intimates, loungewear and modest fashion through TeamAkeem Ladies Wears, and rejuvenating self-care, holistic therapies and premium cosmetics at TeamAkeem Ladies Beauty & Wellness Spa. With professional home service and dedicated salon spa care, we believe healing starts in your mind.', 'group' => 'about', 'type' => 'textarea', 'desc' => 'About Business Story'],
        ];

        foreach ($settings as $key => $data) {
            Setting::set($key, $data['value'], $data['group'], $data['type'], $data['desc']);
        }
    }

    protected function seedCategoriesAndProducts(): void
    {
        $productsDir = storage_path('app/public/products');
        if (!File::exists($productsDir)) {
            File::makeDirectory($productsDir, 0755, true);
        }

        // 1. Ladies Wears Category
        $clothingCat = Category::updateOrCreate(
            ['slug' => 'clothing'],
            [
                'name' => 'Ladies Wears & Underwear',
                'brand_name' => 'TeamAkeem Ladies Wears',
                'subtitle' => 'Luxury Underwear, Nightwear, Intimates & Everyday Style',
                'description' => 'Explore everything ladies: premium supportive wireless bras, seamless briefs, silk kimono robes, lace lingerie sets, and flattering everyday loungewear.',
                'image' => 'products/ladies/ladies_1.jpg',
                'banner_image' => 'products/ladies/ladies_1.jpg',
                'icon' => 'Shirt',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        // 2. Beauty & Wellness Spa Category
        $spaCat = Category::updateOrCreate(
            ['slug' => 'confectionery'], // Keep slug 'confectionery' for backwards database compatibility & seamless routing
            [
                'name' => 'Beauty & Wellness Spa',
                'brand_name' => 'TeamAkeem Beauty & Wellness Spa',
                'subtitle' => 'Rejuvenating Body Massages, Holistic Care & Glow Cosmetics',
                'description' => 'Indulge in professional wellness treatments: full body massages, O-shot & intimate repair, pedicures, waxing, herbal scrubs, cosmetic dental whitening, traditional Gyaran Jiki, and affordable beauty cosmetics. We do home service.',
                'image' => 'products/spa/spa_body_massage.jpg',
                'banner_image' => 'products/spa/spa_facial_skincare.jpg',
                'icon' => 'Sparkles',
                'is_active' => true,
                'display_order' => 2,
            ]
        );

        // Define Ladies Products from resources/ladies (ladies_1.jpg to ladies_14.jpg)
        $clothingProducts = [
            [
                'name' => 'Seamless Sculpting High-Waist Briefs (3-Pack)',
                'slug' => 'seamless-sculpting-high-waist-briefs',
                'sku' => 'TK-UND-001',
                'product_type' => 'underwear',
                'short_description' => 'Ultra-soft bonded seamless high-rise shaping panties offering invisible contour under fitted clothes.',
                'full_description' => "Crafted from breathable second-skin microfiber with a 100% antibacterial cotton gusset. No panty lines, zero digging, and all-day smoothing comfort.\n\n• Laser-cut edges for a 100% invisible silhouette\n• Light-to-medium midsection contouring\n• Breathable pure cotton crotch lining\n• Pack of 3 versatile neutral tones",
                'base_price' => 15000.00,
                'sale_price' => 13500.00,
                'cost_price' => 7000.00,
                'stock_quantity' => 45,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 1,
                'image_path' => 'products/ladies/ladies_1.jpg',
                'video_index' => 1,
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'Extra Large', '2XL'],
                    'Colour Set' => ['Neutral Trio (Black, Nude, Caramel)', 'Classic Monochrome (2 Black, 1 White)', 'Blush Palette (Rose, Warm Sand, Cocoa)'],
                ],
                'variants' => [
                    ['title' => 'Medium / Neutral Trio', 'price' => 13500.00, 'sku' => 'TK-UND-001-M-NT'],
                    ['title' => 'Large / Neutral Trio', 'price' => 13500.00, 'sku' => 'TK-UND-001-L-NT'],
                    ['title' => 'XL / Neutral Trio', 'price' => 14000.00, 'sku' => 'TK-UND-001-XL-NT'],
                    ['title' => 'Medium / Classic Monochrome', 'price' => 13500.00, 'sku' => 'TK-UND-001-M-CM'],
                ],
                'addons' => [
                    ['name' => 'Luxury Gift Pouch & Satin Ribbon', 'price' => 2000.00],
                    ['name' => 'Antibacterial Lingerie Wash Bag', 'price' => 1500.00],
                ],
            ],
            [
                'name' => 'Luxury Mulberry Silk Robe & Chemise Duo',
                'slug' => 'luxury-silk-robe-chemise-duo',
                'sku' => 'TK-SLK-002',
                'product_type' => 'nightwear',
                'short_description' => 'Opulent glossy silk-satin dressing kimono paired with a flattering lace-trimmed nightslip.',
                'full_description' => "Embrace effortless royalty at home. The flowing kimono robe features wide three-quarter sleeves and a detachable sash, paired with a bias-cut chemise slip.\n\n• Lustrous silk-touch finish\n• Delicately scalloped eyelash lace neckline\n• Adjustable spaghetti straps\n• Includes wrap belt with inner modesty ties",
                'base_price' => 38000.00,
                'sale_price' => 34000.00,
                'cost_price' => 18000.00,
                'stock_quantity' => 25,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 2,
                'image_path' => 'products/ladies/ladies_2.jpg',
                'video_index' => 2,
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'XL'],
                    'Colour' => ['Royal Emerald', 'Burgundy Wine', 'Midnight Onyx', 'Golden Champagne'],
                ],
                'variants' => [
                    ['title' => 'Medium / Royal Emerald', 'price' => 34000.00, 'sku' => 'TK-SLK-002-M-EM'],
                    ['title' => 'Large / Burgundy Wine', 'price' => 34000.00, 'sku' => 'TK-SLK-002-L-BW'],
                    ['title' => 'Medium / Midnight Onyx', 'price' => 34000.00, 'sku' => 'TK-SLK-002-M-MO'],
                ],
                'addons' => [
                    ['name' => 'Custom Monogram Embroidered Initial', 'price' => 3000.00],
                    ['name' => 'Matching Silk Sleep Eye Mask', 'price' => 2500.00],
                ],
            ],
            [
                'name' => 'Delicate Floral Lace Bralette & Panty Set',
                'slug' => 'floral-lace-bralette-panty-set',
                'sku' => 'TK-LNG-003',
                'product_type' => 'lingerie',
                'short_description' => 'Unlined romantic botanical floral lace plunge bralette with matching cheeky lace panties.',
                'full_description' => "Designed for exquisite sensuality and pure everyday softness. Wireless comfort with gentle natural lift.\n\n• Scalloped lace hem with soft elastic underband\n• Matching hipster lace panty with cotton gusset\n• Non-scratchy premium stretch lace hand-feel",
                'base_price' => 22000.00,
                'sale_price' => 19500.00,
                'cost_price' => 10000.00,
                'stock_quantity' => 30,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 3,
                'image_path' => 'products/ladies/ladies_3.jpg',
                'video_index' => 3,
                'options' => [
                    'Size' => ['Small (32B-34B)', 'Medium (34B-36B)', 'Large (36C-38C)', 'XL (38C-40D)'],
                    'Colour' => ['Rouge Red', 'Pure Ivory', 'Onyx Black', 'Soft Lavender'],
                ],
                'variants' => [
                    ['title' => 'Medium / Rouge Red', 'price' => 19500.00, 'sku' => 'TK-LNG-003-M-RR'],
                    ['title' => 'Large / Rouge Red', 'price' => 19500.00, 'sku' => 'TK-LNG-003-L-RR'],
                    ['title' => 'Medium / Onyx Black', 'price' => 19500.00, 'sku' => 'TK-LNG-003-M-OB'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Everyday Wireless Cloud Comfort T-Shirt Bra',
                'slug' => 'everyday-wireless-cloud-comfort-bra',
                'sku' => 'TK-BRA-004',
                'product_type' => 'underwear',
                'short_description' => 'Lightly molded memory foam wireless bra delivering natural lift, support, and zero poke.',
                'full_description' => "Say goodbye to restrictive underwires. Ultra-soft contoured memory foam cups mold smoothly to your curves.\n\n• Wire-free comfort with reinforced band support\n• Wide, anti-slip convertible straps\n• Four-hook adjustable back clasp\n• Seamless under T-shirts and fitted tops",
                'base_price' => 18000.00,
                'sale_price' => null,
                'cost_price' => 9000.00,
                'stock_quantity' => 40,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 4,
                'image_path' => 'products/ladies/ladies_4.jpg',
                'video_index' => 4,
                'options' => [
                    'Band & Cup Size' => ['34B', '34C', '36B', '36C', '38B', '38C', '40C', '42D'],
                    'Colour' => ['Deep Cocoa', 'Warm Sand', 'Midnight Black', 'Dusty Rose'],
                ],
                'variants' => [
                    ['title' => '36B / Deep Cocoa', 'price' => 18000.00, 'sku' => 'TK-BRA-004-36B-DC'],
                    ['title' => '36C / Warm Sand', 'price' => 18000.00, 'sku' => 'TK-BRA-004-36C-WS'],
                    ['title' => '38C / Midnight Black', 'price' => 18000.00, 'sku' => 'TK-BRA-004-38C-MB'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Classic Ribbed Cotton Loungewear Two-Piece Set',
                'slug' => 'classic-ribbed-cotton-loungewear-set',
                'sku' => 'TK-LNG-005',
                'product_type' => 'clothing',
                'short_description' => 'Cozy 100% breathable ribbed cotton set featuring button-front henley top and wide-leg drawstring pants.',
                'full_description' => "The quintessential off-duty uniform for modern women. Ultra-soft ribbed texture that stretches effortlessly.\n\n• Relaxed fit with tortoiseshell button placket\n• Deep side seam pockets on pants\n• Lightweight yet opaque fabric\n• Designed for all-day comfort at home or casual outings",
                'base_price' => 28000.00,
                'sale_price' => 25000.00,
                'cost_price' => 13000.00,
                'stock_quantity' => 20,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 5,
                'image_path' => 'products/ladies/ladies_5.jpg',
                'video_index' => 5,
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
                    'Colour' => ['Warm Oatmeal', 'Sage Mist', 'Chocolate Espresso', 'Terracotta Orange'],
                ],
                'variants' => [
                    ['title' => 'Medium / Warm Oatmeal', 'price' => 25000.00, 'sku' => 'TK-LNG-005-M-WO'],
                    ['title' => 'Large / Sage Mist', 'price' => 25000.00, 'sku' => 'TK-LNG-005-L-SM'],
                    ['title' => 'Medium / Terracotta Orange', 'price' => 25000.00, 'sku' => 'TK-LNG-005-M-TO'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'All-Day Bodyshaper Open-Bust Contour Suit',
                'slug' => 'all-day-bodyshaper-open-bust-contour-suit',
                'sku' => 'TK-SHP-006',
                'product_type' => 'shapewear',
                'short_description' => 'Targeted compression bodysuit that flattens tummy, lifts buttocks, and smooths waistline with open-bust design.',
                'full_description' => "Engineered with double-layered powernet compression. Wear your own favourite bra while achieving an instantly sleek, hourglass silhouette.\n\n• Mid-thigh length prevents chafing\n• Convenient bathroom opening gusset\n• Anti-roll silicone leg grip\n• Seamless under evening dresses and bodycon styles",
                'base_price' => 32000.00,
                'sale_price' => 29000.00,
                'cost_price' => 15000.00,
                'stock_quantity' => 35,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 6,
                'image_path' => 'products/ladies/ladies_6.jpg',
                'video_index' => 6,
                'options' => [
                    'Size' => ['Medium (UK 10-12)', 'Large (UK 14-16)', 'XL (UK 18-20)', '2XL (UK 22-24)'],
                    'Colour' => ['Onyx Black', 'Cafe Au Lait Nude'],
                ],
                'variants' => [
                    ['title' => 'Medium / Onyx Black', 'price' => 29000.00, 'sku' => 'TK-SHP-006-M-OB'],
                    ['title' => 'Large / Onyx Black', 'price' => 29000.00, 'sku' => 'TK-SHP-006-L-OB'],
                    ['title' => 'Large / Cafe Au Lait Nude', 'price' => 29000.00, 'sku' => 'TK-SHP-006-L-CN'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Flattering Satin Cami & Tap Shorts Pajama Set',
                'slug' => 'satin-cami-tap-shorts-pajama-set',
                'sku' => 'TK-PJM-007',
                'product_type' => 'nightwear',
                'short_description' => 'Silky smooth camisole with delicate lace inserts and matching relaxed elasticated flutter shorts.',
                'full_description' => "Breeze through warm nights in luxurious, lightweight satin that feels gentle against bare skin.\n\n• Adjustable racerback straps\n• Scalloped lace trims along shorts hem\n• Machine washable on delicate cycle",
                'base_price' => 20000.00,
                'sale_price' => 17500.00,
                'cost_price' => 9000.00,
                'stock_quantity' => 28,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 7,
                'image_path' => 'products/ladies/ladies_7.jpg',
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
                    'Colour' => ['Blush Pink', 'Emerald Green', 'Champagne Gold', 'Midnight Navy'],
                ],
                'variants' => [
                    ['title' => 'Medium / Blush Pink', 'price' => 17500.00, 'sku' => 'TK-PJM-007-M-BP'],
                    ['title' => 'Large / Emerald Green', 'price' => 17500.00, 'sku' => 'TK-PJM-007-L-EG'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Pure Cotton Breathable Sleep Shorts (Pack of 2)',
                'slug' => 'pure-cotton-breathable-sleep-shorts',
                'sku' => 'TK-SHT-008',
                'product_type' => 'clothing',
                'short_description' => 'Casual relaxed boyfriend-cut boxer shorts crafted from 100% natural airy cotton poplin.',
                'full_description' => "Ultimate breathable comfort for sleeping, lounging, or wearing under dresses. Pack includes 2 coordinating shorts.\n\n• Elasticated waistband with contrast satin drawstring\n• Non-restricting relaxed leg opening\n• Soft pre-shrunk cotton fabric",
                'base_price' => 14000.00,
                'sale_price' => 12000.00,
                'cost_price' => 6000.00,
                'stock_quantity' => 30,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 8,
                'image_path' => 'products/ladies/ladies_8.jpg',
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'XL'],
                    'Set Style' => ['Stripe & Solid Duo', 'Pastel Florals Duo', 'Black & Grey Essentials'],
                ],
                'variants' => [
                    ['title' => 'Medium / Stripe & Solid Duo', 'price' => 12000.00, 'sku' => 'TK-SHT-008-M-SS'],
                    ['title' => 'Large / Stripe & Solid Duo', 'price' => 12000.00, 'sku' => 'TK-SHT-008-L-SS'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Sweetheart Lace Halter Babydoll Nightdress',
                'slug' => 'sweetheart-lace-halter-babydoll',
                'sku' => 'TK-BBY-009',
                'product_type' => 'lingerie',
                'short_description' => 'Romantic sheer floral mesh babydoll with soft supportive lace cups and matching G-string.',
                'full_description' => "Sultry, romantic silhouette with an empire waistline that cascades into an airy, flyaway hem.\n\n• Adjustable tie-up halter neck\n• Delicate satin bow centerpiece\n• Includes coordinating minimal thong",
                'base_price' => 24000.00,
                'sale_price' => null,
                'cost_price' => 11000.00,
                'stock_quantity' => 22,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 9,
                'image_path' => 'products/ladies/ladies_9.jpg',
                'options' => [
                    'Size' => ['Small/Medium (UK 8-12)', 'Large/XL (UK 14-18)'],
                    'Colour' => ['Crimson Red', 'Velvet Noir', 'Snow White'],
                ],
                'variants' => [
                    ['title' => 'Small/Medium / Crimson Red', 'price' => 24000.00, 'sku' => 'TK-BBY-009-SM-CR'],
                    ['title' => 'Large/XL / Velvet Noir', 'price' => 24000.00, 'sku' => 'TK-BBY-009-LXL-VN'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'High-Impact Wireless Support Sports Bra',
                'slug' => 'high-impact-wireless-support-sports-bra',
                'sku' => 'TK-SPT-010',
                'product_type' => 'underwear',
                'short_description' => 'Maximum bounce control wireless athletic bra with moisture-wicking compression and criss-cross back.',
                'full_description' => "Designed for confident movement during workouts, active walks, and busy daily routines.\n\n• Wide cushioned shoulder straps\n• Breathable mesh ventilation zones\n• Removable contour cups for customizable coverage",
                'base_price' => 19500.00,
                'sale_price' => 17000.00,
                'cost_price' => 8500.00,
                'stock_quantity' => 35,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 10,
                'image_path' => 'products/ladies/ladies_10.jpg',
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'Extra Large'],
                    'Colour' => ['Pitch Black', 'Slate Grey', 'Vibrant Orange'],
                ],
                'variants' => [
                    ['title' => 'Medium / Pitch Black', 'price' => 17000.00, 'sku' => 'TK-SPT-010-M-PB'],
                    ['title' => 'Large / Pitch Black', 'price' => 17000.00, 'sku' => 'TK-SPT-010-L-PB'],
                    ['title' => 'Medium / Vibrant Orange', 'price' => 17000.00, 'sku' => 'TK-SPT-010-M-VO'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Mid-Waist Laser Cut Invisible Briefs (Set of 5)',
                'slug' => 'mid-waist-laser-cut-invisible-briefs',
                'sku' => 'TK-UND-011',
                'product_type' => 'underwear',
                'short_description' => 'Everyday essential 5-pack of buttery-smooth laser-cut briefs with 100% cotton inner gusset.',
                'full_description' => "The absolute foundational necessity for every lady's wardrobe. Sleek, featherlight, and completely imperceptible.\n\n• 5 Pairs in assorted neutral and earthy shades\n• Tag-free for zero itch\n• Quick-drying fabric perfect for Nigerian climate",
                'base_price' => 22000.00,
                'sale_price' => 19000.00,
                'cost_price' => 9500.00,
                'stock_quantity' => 50,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 11,
                'image_path' => 'products/ladies/ladies_11.jpg',
                'options' => [
                    'Size' => ['Small', 'Medium', 'Large', 'XL', '2XL'],
                ],
                'variants' => [
                    ['title' => 'Medium (UK 10-12)', 'price' => 19000.00, 'sku' => 'TK-UND-011-M'],
                    ['title' => 'Large (UK 14-16)', 'price' => 19000.00, 'sku' => 'TK-UND-011-L'],
                    ['title' => 'XL (UK 18-20)', 'price' => 20000.00, 'sku' => 'TK-UND-011-XL'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Modest Floor-Length Silk Loungewear Kimono Abaya',
                'slug' => 'modest-floor-length-silk-loungewear-kimono',
                'sku' => 'TK-ABY-012',
                'product_type' => 'clothing',
                'short_description' => 'Sweeping, graceful lightweight kimono duster with bell sleeves and tonal modesty waist tie.',
                'full_description' => "Versatile elegance designed for modest living. Slip it on over house clothes for guests or wear with confidence as an elevated outerwear layer.\n\n• Floor-length flowing silhouette\n• Includes matching wide sash\n• Wrinkle-resistant silky crepe blend",
                'base_price' => 42000.00,
                'sale_price' => 38000.00,
                'cost_price' => 20000.00,
                'stock_quantity' => 18,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 12,
                'image_path' => 'products/ladies/ladies_12.jpg',
                'options' => [
                    'Length / Size' => ['54 inch (Standard Petite)', '56 inch (Regular)', '58 inch (Tall)'],
                    'Colour' => ['Rich Espresso', 'Onyx Black', 'Warm Terracotta', 'Olive Bronze'],
                ],
                'variants' => [
                    ['title' => '56 inch / Rich Espresso', 'price' => 38000.00, 'sku' => 'TK-ABY-012-56-RE'],
                    ['title' => '56 inch / Onyx Black', 'price' => 38000.00, 'sku' => 'TK-ABY-012-56-OB'],
                    ['title' => '58 inch / Warm Terracotta', 'price' => 39000.00, 'sku' => 'TK-ABY-012-58-WT'],
                ],
                'addons' => [
                    ['name' => 'Matching Silk Chiffon Hijab / Shawl', 'price' => 4500.00],
                ],
            ],
            [
                'name' => 'Tummy-Flattening High-Waist Shaper Shorts',
                'slug' => 'tummy-flattening-high-waist-shaper-shorts',
                'sku' => 'TK-SHP-013',
                'product_type' => 'shapewear',
                'short_description' => 'Firm-control shaping shorts with anti-slip bone supports that eliminate muffin-top and thigh rub.',
                'full_description' => "Instant silhouette confidence. Features 4 flexible steel bones along the waistband to prevent rolling down.\n\n• High-waist design reaches right below bra line\n• Mid-thigh chaffing prevention\n• Cotton crotch lining for breathable wear",
                'base_price' => 22000.00,
                'sale_price' => 19500.00,
                'cost_price' => 10000.00,
                'stock_quantity' => 32,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 13,
                'image_path' => 'products/ladies/ladies_13.jpg',
                'options' => [
                    'Size' => ['Small/Medium', 'Large/XL', '2XL/3XL'],
                    'Colour' => ['Black', 'Warm Beige Nude'],
                ],
                'variants' => [
                    ['title' => 'Large/XL / Black', 'price' => 19500.00, 'sku' => 'TK-SHP-013-LXL-BK'],
                    ['title' => 'Large/XL / Warm Beige Nude', 'price' => 19500.00, 'sku' => 'TK-SHP-013-LXL-BN'],
                ],
                'addons' => [],
            ],
            [
                'name' => 'Satin Push-Up Corset Lace-Up Bralette',
                'slug' => 'satin-push-up-corset-lace-up-bralette',
                'sku' => 'TK-COR-014',
                'product_type' => 'lingerie',
                'short_description' => 'Structured boned satin bustier top with adjustable lace-up back and supportive underwire cups.',
                'full_description' => "A show-stopping piece you can style as luxury lingerie or pair under blazers for high-fashion elegance.\n\n• Reinforced flexible boning\n• Padded underwire push-up cups\n• Premium ribbon tie-up back for custom tightening",
                'base_price' => 28000.00,
                'sale_price' => 25000.00,
                'cost_price' => 13000.00,
                'stock_quantity' => 20,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 14,
                'image_path' => 'products/ladies/ladies_14.jpg',
                'options' => [
                    'Size' => ['Small (32-34)', 'Medium (34-36)', 'Large (36-38)'],
                    'Colour' => ['Deep Scarlet', 'Midnight Obsidian', 'Champagne Pearl'],
                ],
                'variants' => [
                    ['title' => 'Medium / Deep Scarlet', 'price' => 25000.00, 'sku' => 'TK-COR-014-M-DS'],
                    ['title' => 'Large / Midnight Obsidian', 'price' => 25000.00, 'sku' => 'TK-COR-014-L-MO'],
                ],
                'addons' => [],
            ],
        ];

        // Define Spa Services & Cosmetics Products
        $spaProducts = [
            [
                'name' => '1. Full Body Relaxing & Aromatherapy Massage',
                'slug' => 'full-body-relaxing-massage',
                'sku' => 'TK-SPA-001',
                'product_type' => 'massage',
                'short_description' => 'Deeply restorative full body Swedish & deep tissue massage using warm botanical oils and basalt stones.',
                'full_description' => "Melt away fatigue, tension, and stress. Our certified female massage therapists combine gentle long strokes, target pressure points, and warm essential oils to awaken natural vitality.\n\n• Full body coverage (back, neck, shoulders, legs, arms & scalp)\n• Choice of organic lavender, frankincense or eucalyptus oil\n• Warm basalt stone therapy included\n• In-spa studio session or professional home service available",
                'base_price' => 25000.00,
                'sale_price' => 22000.00,
                'cost_price' => 8000.00,
                'stock_quantity' => 100,
                'track_stock' => false,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 1,
                'image_path' => 'products/spa/spa_body_massage.jpg',
                'options' => [
                    'Session Duration' => ['60 Minutes Relaxing Massage', '90 Minutes Deluxe Massage', '120 Minutes VIP Deep Relief'],
                    'Service Type' => ['In-Studio Spa Session', 'Home Service (We Come To You)'],
                ],
                'variants' => [
                    ['title' => '60 Mins / In-Studio Spa Session', 'price' => 22000.00, 'sku' => 'TK-SPA-001-60-STU'],
                    ['title' => '90 Mins / In-Studio Spa Session', 'price' => 30000.00, 'sku' => 'TK-SPA-001-90-STU'],
                    ['title' => '60 Mins / Home Service', 'price' => 32000.00, 'sku' => 'TK-SPA-001-60-HOM'],
                    ['title' => '90 Mins / Home Service', 'price' => 42000.00, 'sku' => 'TK-SPA-001-90-HOM'],
                ],
                'addons' => [
                    ['name' => 'Warm Volcanic Basalt Stones Extension', 'price' => 4000.00],
                    ['name' => 'Relaxing Scalp & Foot Reflexology Scrub', 'price' => 5000.00],
                ],
            ],
            [
                'name' => '2. O-Shot Rejuvenation & Normal Repair Service',
                'slug' => 'o-shot-rejuvenation-normal-repair',
                'sku' => 'TK-SPA-002',
                'product_type' => 'treatment',
                'short_description' => 'Discreet, specialized feminine intimate wellness rejuvenation and pelvic tissue normal repair therapy.',
                'full_description' => "Dedicated confidential wellness protocol focused on female intimate restoration, enhanced sensitivity, muscular toning, and healthy cellular repair. Delivered in absolute privacy with gentle, sterile care.\n\n• Confidential consultation & assessment\n• Restorative rejuvenation & normal tissue recovery\n• Helps postpartum tightening, comfort & pelvic wellness\n• Strict privacy & high hygiene standards",
                'base_price' => 50000.00,
                'sale_price' => 45000.00,
                'cost_price' => 15000.00,
                'stock_quantity' => 50,
                'track_stock' => false,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 2,
                'image_path' => 'products/spa/flyer_treatment.jpg',
                'options' => [
                    'Treatment Protocol' => ['Standard Intimate Repair Session', 'Intensive Restorative Rejuvenation & Consultation'],
                    'Location' => ['In-Studio Private Suite', 'Home Service VIP Appointment'],
                ],
                'variants' => [
                    ['title' => 'Standard Repair / In-Studio', 'price' => 45000.00, 'sku' => 'TK-SPA-002-STD-STU'],
                    ['title' => 'Intensive Rejuvenation / In-Studio', 'price' => 65000.00, 'sku' => 'TK-SPA-002-INT-STU'],
                    ['title' => 'Intensive Rejuvenation / Home Service VIP', 'price' => 85000.00, 'sku' => 'TK-SPA-002-INT-HOM'],
                ],
                'addons' => [
                    ['name' => 'Follow-up Restorative Herbal Tonic & Care Kit', 'price' => 7500.00],
                ],
            ],
            [
                'name' => '3. Royal Deluxe Pedicure & Manicure Care',
                'slug' => 'royal-deluxe-pedicure-manicure',
                'sku' => 'TK-SPA-003',
                'product_type' => 'pedicure',
                'short_description' => 'Warm rose-petal foot bath, dead skin removal, organic scrub, hot towel wrap, and flawless polish.',
                'full_description' => "Pamper your tired feet and hands to royalty. Begins with a botanical floral soak, callous smoothing, sugar exfoliation, deep moisturizing cuticle care, and your choice of long-lasting nail finish.\n\n• Rose petal & essential oil foot bath\n• Full cuticle trimming & buffing\n• Relaxing foot & lower calf massage\n• Halal breathable polish or long-wear gel finish",
                'base_price' => 18000.00,
                'sale_price' => 15000.00,
                'cost_price' => 5000.00,
                'stock_quantity' => 100,
                'track_stock' => false,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 3,
                'image_path' => 'products/spa/spa_pedicure_manicure.jpg',
                'options' => [
                    'Service Package' => ['Classic Pedicure & Manicure Combo', 'Deluxe Paraffin Wax Pedicure & Manicure', 'Pedicure Only (Foot Spa)'],
                    'Polish Type' => ['Halal Breathable Polish', 'Gel UV Long-Wear Finish', 'Natural Buff & Shine'],
                ],
                'variants' => [
                    ['title' => 'Classic Combo / Halal Breathable', 'price' => 15000.00, 'sku' => 'TK-SPA-003-CC-HB'],
                    ['title' => 'Classic Combo / Gel UV Long-Wear', 'price' => 18000.00, 'sku' => 'TK-SPA-003-CC-GL'],
                    ['title' => 'Deluxe Paraffin Combo / Gel Finish', 'price' => 24000.00, 'sku' => 'TK-SPA-003-DP-GL'],
                ],
                'addons' => [
                    ['name' => 'Callus Peel Intensive Foot Treatment', 'price' => 3500.00],
                    ['name' => 'Custom Nail Art (2 Accent Nails)', 'price' => 2000.00],
                ],
            ],
            [
                'name' => '4. Silky Waxing & Herbal Body Scrub Exfoliation',
                'slug' => 'waxing-and-herbal-body-scrub',
                'sku' => 'TK-SPA-004',
                'product_type' => 'waxing',
                'short_description' => 'Gentle hair removal waxing paired with dead skin cell exfoliation and turmeric glow body scrub.',
                'full_description' => "Reveal baby-soft, luminous skin. Warm honey/sugar waxing removes hair cleanly from the root with minimal discomfort, followed by our signature clarifying herbal polish.\n\n• Choice of body areas (Underarms, Bikini/Brazilian, Full Legs, Full Body)\n• All-natural organic sugar scrub infused with turmeric and honey\n• Post-wax calming botanical serum to eliminate ingrown hairs",
                'base_price' => 24000.00,
                'sale_price' => 20000.00,
                'cost_price' => 7000.00,
                'stock_quantity' => 80,
                'track_stock' => false,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 4,
                'image_path' => 'products/spa/spa_waxing_scrub.jpg',
                'options' => [
                    'Service Area' => ['Full Body Exfoliating Scrub Only', 'Underarms + Bikini Waxing + Scrub', 'Full Body Waxing & Glow Scrub Package'],
                ],
                'variants' => [
                    ['title' => 'Full Body Scrub Only', 'price' => 18000.00, 'sku' => 'TK-SPA-004-BS-ONLY'],
                    ['title' => 'Underarms + Bikini + Scrub', 'price' => 22000.00, 'sku' => 'TK-SPA-004-UB-SCR'],
                    ['title' => 'Full Body Waxing & Glow Scrub Package', 'price' => 38000.00, 'sku' => 'TK-SPA-004-FB-PKG'],
                ],
                'addons' => [
                    ['name' => 'Brightening Turmeric Underarm Mask', 'price' => 3000.00],
                ],
            ],
            [
                'name' => '5. Cosmetic Teeth Whitening & Dental Spa Hygiene',
                'slug' => 'cosmetic-teeth-whitening-dental-services',
                'sku' => 'TK-SPA-005',
                'product_type' => 'dental',
                'short_description' => 'Gentle non-peroxide LED light teeth whitening and ultrasonic cleansing for a dazzling, confident smile.',
                'full_description' => "Brighten your smile up to 5 to 8 shades in a single safe, pain-free appointment. Eliminates stubborn stains from coffee, tea, and everyday life.\n\n• Zero enamel damage or post-treatment sensitivity\n• Gentle blue LED activation formula\n• Immediate sparkling results in 45 minutes",
                'base_price' => 30000.00,
                'sale_price' => 26000.00,
                'cost_price' => 9000.00,
                'stock_quantity' => 60,
                'track_stock' => false,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 5,
                'image_path' => 'products/spa/spa_dental.jpg',
                'options' => [
                    'Session Package' => ['Express 45-Minute Smile Brightening', 'Intensive Dual-Cycle Laser Whitening (60 Mins)'],
                ],
                'variants' => [
                    ['title' => 'Express 45-Min Brightening', 'price' => 26000.00, 'sku' => 'TK-SPA-005-EXP-45'],
                    ['title' => 'Intensive Dual-Cycle 60-Min', 'price' => 42000.00, 'sku' => 'TK-SPA-005-INT-60'],
                ],
                'addons' => [
                    ['name' => 'Take-Home Whitening Maintenance Gel Pen', 'price' => 5500.00],
                ],
            ],
            [
                'name' => '6. Traditional Northern & Modern SPA (Gyaran Jiki)',
                'slug' => 'traditional-modern-spa-gyaran-jiki',
                'sku' => 'TK-SPA-006',
                'product_type' => 'traditional_spa',
                'short_description' => 'Authentic Northern Hausa bridal & queens beauty ritual: herbal steam (Dukhan), scented body smoke, scrub & oils.',
                'full_description' => "Our signature cultural masterpiece. Gyaran Jiki is an ancient restorative regimen treasured across generations for preparing brides and women for pure radiance.\n\n• Herbal steam bath (Dukhan) infused with exotic woods and cloves\n• Traditional Halawa organic sugar polish\n• Scented body smoke ceremony (Turaren Wuta) that leaves hair and skin perfumed for days\n• Deeply nourishing Humra & scented shea glaze",
                'base_price' => 40000.00,
                'sale_price' => 35000.00,
                'cost_price' => 12000.00,
                'stock_quantity' => 40,
                'track_stock' => false,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 6,
                'image_path' => 'products/spa/spa_traditional_gyaran.jpg',
                'options' => [
                    'Treatment Package' => ['1-Day Signature Gyaran Jiki Glow', '3-Day Comprehensive Bridal Sanctuary Protocol'],
                    'Service Type' => ['In-Studio Private Sanctuary', 'VIP Home Service Setup'],
                ],
                'variants' => [
                    ['title' => '1-Day Signature Glow / In-Studio', 'price' => 35000.00, 'sku' => 'TK-SPA-006-1D-STU'],
                    ['title' => '1-Day Signature Glow / Home Service', 'price' => 48000.00, 'sku' => 'TK-SPA-006-1D-HOM'],
                    ['title' => '3-Day Comprehensive Bridal Package', 'price' => 95000.00, 'sku' => 'TK-SPA-006-3D-BRD'],
                ],
                'addons' => [
                    ['name' => 'Signature Artisanal Turaren Wuta Incense Jar (100g)', 'price' => 6000.00],
                    ['name' => 'Traditional Perfumed Humra Oil (30ml)', 'price' => 4500.00],
                ],
            ],
            [
                'name' => '7. Luminous Glow Vitamin C & Hyaluronic Facial Serum',
                'slug' => 'luminous-glow-vitamin-c-serum',
                'sku' => 'TK-COS-007',
                'product_type' => 'cosmetics',
                'short_description' => 'Brightening daily antioxidant face serum that clears blemishes, evens skin tone, and restores plump moisture.',
                'full_description' => "Formulated specifically for melanin-rich skin. Combines 15% active Vitamin C with multi-molecular hyaluronic acid to fade acne marks, hyperpigmentation, and sun spots.\n\n• Lightweight non-greasy absorption\n• Restores natural dewy glass-skin glow\n• Dermatologist tested for all skin types",
                'base_price' => 9000.00,
                'sale_price' => 7500.00,
                'cost_price' => 3500.00,
                'stock_quantity' => 120,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 7,
                'image_path' => 'products/spa/spa_cosmetics_beauty.jpg',
                'options' => [
                    'Bottle Size' => ['30ml Glass Dropper Bottle', '50ml Value Size Bottle'],
                ],
                'variants' => [
                    ['title' => '30ml Dropper Bottle', 'price' => 7500.00, 'sku' => 'TK-COS-007-30ML'],
                    ['title' => '50ml Value Size', 'price' => 11500.00, 'sku' => 'TK-COS-007-50ML'],
                ],
                'addons' => [],
            ],
            [
                'name' => '8. Whipped Shea & Turmeric Radiant Body Butter',
                'slug' => 'whipped-shea-turmeric-body-butter',
                'sku' => 'TK-COS-008',
                'product_type' => 'cosmetics',
                'short_description' => 'Ultra-rich unrefined organic shea butter whipped with wild turmeric, rosehip oil, and warm vanilla.',
                'full_description' => "Deeply nourishes dry skin, fades dark knees and elbows, and locks in velvety 24-hour hydration.\n\n• 100% natural organic unrefined African shea butter\n• Infused with turmeric root extract for radiant skin tone\n• Melts effortlessly upon touch with zero greasy residue",
                'base_price' => 7000.00,
                'sale_price' => 5500.00,
                'cost_price' => 2500.00,
                'stock_quantity' => 90,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 8,
                'image_path' => 'products/spa/spa_waxing_scrub.jpg',
                'options' => [
                    'Jar Size' => ['250g Jar', '500g Family Tub'],
                    'Fragrance' => ['Warm Vanilla & Sweet Almond', 'Arabian Oud & Amber', 'Unscented Sensitive Skin'],
                ],
                'variants' => [
                    ['title' => '250g Jar / Warm Vanilla', 'price' => 5500.00, 'sku' => 'TK-COS-008-250-WV'],
                    ['title' => '250g Jar / Arabian Oud', 'price' => 5500.00, 'sku' => 'TK-COS-008-250-AO'],
                    ['title' => '500g Tub / Warm Vanilla', 'price' => 9500.00, 'sku' => 'TK-COS-008-500-WV'],
                ],
                'addons' => [],
            ],
            [
                'name' => '9. Pure Steam-Distilled Rosewater Facial Mist & Toner',
                'slug' => 'pure-rosewater-facial-mist-toner',
                'sku' => 'TK-COS-009',
                'product_type' => 'cosmetics',
                'short_description' => 'Refreshing alcohol-free hydrating mist with soothing Damask rosewater and aloe vera.',
                'full_description' => "Balance skin pH, calm redness, and set makeup with this refreshing botanical facial spray.\n\n• Instantly cools skin in hot weather\n• Tightens pores and delivers hydration boost\n• 100% pure alcohol-free botanical formula",
                'base_price' => 5000.00,
                'sale_price' => 4200.00,
                'cost_price' => 1800.00,
                'stock_quantity' => 100,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => false,
                'display_order' => 9,
                'image_path' => 'products/spa/spa_facial_skincare.jpg',
                'options' => [
                    'Bottle Size' => ['100ml Travel Spray Bottle', '250ml Full Bottle Refill'],
                ],
                'variants' => [
                    ['title' => '100ml Travel Spray', 'price' => 4200.00, 'sku' => 'TK-COS-009-100ML'],
                    ['title' => '250ml Full Bottle', 'price' => 7500.00, 'sku' => 'TK-COS-009-250ML'],
                ],
                'addons' => [],
            ],
            [
                'name' => '10. Herbal Moroccan Black Soap & Exfoliating Kessa Glove',
                'slug' => 'herbal-moroccan-black-soap-kessa-glove',
                'sku' => 'TK-COS-010',
                'product_type' => 'cosmetics',
                'short_description' => 'Traditional eucalyptus black soap paste and authentic crepe kessa mitt for instant dead skin roll-off.',
                'full_description' => "The secret to satin-soft skin. Apply in a steamy shower, allow eucalyptus oils to open pores, then buff with the kessa glove to visibly sweep away dead skin cells.\n\n• Rich in Vitamin E and organic olive oil\n• Deeply cleanses and refines skin texture\n• Includes authentic reusable Moroccan kessa mitt",
                'base_price' => 6500.00,
                'sale_price' => 5000.00,
                'cost_price' => 2200.00,
                'stock_quantity' => 85,
                'track_stock' => true,
                'is_available' => true,
                'is_featured' => true,
                'display_order' => 10,
                'image_path' => 'products/spa/spa_cosmetics_beauty.jpg',
                'options' => [
                    'Set Option' => ['Complete Set (250g Soap + Kessa Glove)', 'Soap 500g Refill Tub (No Glove)'],
                ],
                'variants' => [
                    ['title' => 'Complete Set with Kessa Glove', 'price' => 5000.00, 'sku' => 'TK-COS-010-SET'],
                    ['title' => 'Soap 500g Tub Only', 'price' => 6500.00, 'sku' => 'TK-COS-010-TUB500'],
                ],
                'addons' => [],
            ],
        ];

        // Seed Clothing Products
        $this->createProductsWithMedia($clothingCat, $clothingProducts);

        // Seed Spa Products
        $this->createProductsWithMedia($spaCat, $spaProducts);
    }

    protected function createProductsWithMedia(Category $category, array $products): void
    {
        $productsDir = storage_path('app/public/products');

        foreach ($products as $pData) {
            $imagePath = $pData['image_path'] ?? null;

            // If an image_path is specified and file doesn't exist, check fallback
            if (!$imagePath || !File::exists(storage_path('app/public/' . $imagePath))) {
                $svgFilename = Str::slug($pData['slug']) . '.svg';
                $svgFullPath = $productsDir . '/' . $svgFilename;
                $svgContent = $this->generateProductSvg(
                    $pData['name'],
                    $category->brand_name,
                    $pData['short_description'],
                    '#18181B',
                    '#FA5A00'
                );
                File::put($svgFullPath, $svgContent);
                $imagePath = 'products/' . $svgFilename;
            }

            // Create or Update Product
            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $pData['name'],
                    'sku' => $pData['sku'],
                    'product_type' => $pData['product_type'],
                    'short_description' => $pData['short_description'],
                    'full_description' => $pData['full_description'],
                    'base_price' => $pData['base_price'],
                    'sale_price' => $pData['sale_price'],
                    'cost_price' => $pData['cost_price'],
                    'stock_quantity' => $pData['stock_quantity'],
                    'track_stock' => $pData['track_stock'],
                    'is_available' => $pData['is_available'],
                    'is_featured' => $pData['is_featured'],
                    'display_order' => $pData['display_order'],
                    'meta_title' => $pData['name'] . ' | ' . $category->brand_name,
                    'meta_description' => $pData['short_description'],
                ]
            );

            // Clear old media and attach primary image
            $product->media()->delete();

            ProductMedia::create([
                'product_id' => $product->id,
                'file_path' => $imagePath,
                'media_type' => 'image',
                'alt_text' => $pData['name'],
                'display_order' => 1,
                'is_primary' => true,
            ]);

            // Create Options and Values
            $product->options()->delete();
            $optionMap = [];
            $displayOrder = 1;
            foreach ($pData['options'] as $optName => $values) {
                $option = ProductOption::create([
                    'product_id' => $product->id,
                    'name' => $optName,
                    'display_order' => $displayOrder++,
                ]);

                $valOrder = 1;
                foreach ($values as $valName) {
                    $optValue = ProductOptionValue::create([
                        'product_option_id' => $option->id,
                        'name' => $valName,
                        'display_order' => $valOrder++,
                    ]);
                    $optionMap[$optName][$valName] = $optValue->id;
                }
            }

            // Create Variants
            $product->variants()->delete();
            foreach ($pData['variants'] as $vData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $vData['sku'] ?? null,
                    'title' => $vData['title'],
                    'price' => $vData['price'],
                    'stock_quantity' => 20,
                    'is_available' => true,
                ]);

                // Link option values matching titles
                foreach ($optionMap as $optName => $valMap) {
                    foreach ($valMap as $valName => $optValId) {
                        if (str_contains($vData['title'], $valName)) {
                            $variant->optionValues()->attach($optValId);
                        }
                    }
                }
            }

            // Create Addons
            $product->addons()->delete();
            $addonOrder = 1;
            foreach ($pData['addons'] as $addonData) {
                ProductAddon::create([
                    'product_id' => $product->id,
                    'name' => $addonData['name'],
                    'price' => $addonData['price'],
                    'is_available' => true,
                    'display_order' => $addonOrder++,
                ]);
            }
        }
    }

    protected function generateProductSvg(string $title, string $brand, string $desc, string $bg, string $fg): string
    {
        $shortDesc = Str::limit($desc, 60);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#18181B" />
      <stop offset="100%" stop-color="#0A0A0A" />
    </linearGradient>
  </defs>
  <rect width="800" height="800" fill="url(#bgGrad)" />
  <circle cx="400" cy="380" r="240" fill="#FA5A00" opacity="0.12" filter="blur(40px)" />
  <circle cx="400" cy="380" r="140" fill="none" stroke="#FA5A00" stroke-width="2" stroke-dasharray="6 6" opacity="0.6" />
  <text x="400" y="140" text-anchor="middle" fill="#FA5A00" font-family="'Plus Jakarta Sans', sans-serif" font-size="16" letter-spacing="8" font-weight="600">{$brand}</text>
  <text x="400" y="390" text-anchor="middle" fill="#FA5A00" font-family="'Cinzel', Georgia, serif" font-size="52" font-weight="700">TK Lw</text>
  <text x="400" y="650" text-anchor="middle" fill="#FFFFFF" font-family="'Cinzel', 'Playfair Display', Georgia, serif" font-size="30" font-weight="700">{$title}</text>
  <text x="400" y="695" text-anchor="middle" fill="#A1A1AA" font-family="'Plus Jakarta Sans', sans-serif" font-size="16" font-weight="400">{$shortDesc}</text>
</svg>
SVG;
    }
}
