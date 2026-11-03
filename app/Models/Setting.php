<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::getAllCached();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text', ?string $description = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget('app_settings');

        return $setting;
    }

    public static function getAllCached(): array
    {
        return Cache::rememberForever('app_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    public static function getPublicSettings(): array
    {
        $all = self::getAllCached();

        return [
            'business_name' => $all['business_name'] ?? 'TeamAkeem Ladies World',
            'clothing_brand_name' => $all['clothing_brand_name'] ?? 'TeamAkeem Ladies Wears',
            'confectionery_brand_name' => $all['confectionery_brand_name'] ?? 'TeamAkeem Beauty & Wellness Spa',
            'tagline' => $all['tagline'] ?? 'Luxury Ladies Wears, Intimates & Holistic Wellness Spa',
            'hero_headline' => $all['hero_headline'] ?? 'Empowering Feminine Beauty & Everyday Confidence',
            'hero_subheadline' => $all['hero_subheadline'] ?? 'Explore bespoke ladies intimates, loungewear and modesty collections alongside restorative treatments at TeamAkeem Ladies Beauty and Wellness Spa. Seamless WhatsApp ordering & booking.',
            'whatsapp_number' => $all['whatsapp_number'] ?? '2348076187311',
            'currency_code' => $all['currency_code'] ?? 'NGN',
            'currency_symbol' => $all['currency_symbol'] ?? '₦',
            'contact_email' => $all['contact_email'] ?? 'teemakeemladiesworld@gmail.com',
            'contact_phone' => $all['contact_phone'] ?? '+234 807 618 7311',
            'address' => $all['address'] ?? 'Kano & Lagos, Nigeria (Home Service Available)',
            'instagram_url' => $all['instagram_url'] ?? 'https://instagram.com/teemakeemladiesworld',
            'facebook_url' => $all['facebook_url'] ?? 'https://facebook.com/teemakeemladiesworld',
            'delivery_info' => $all['delivery_info'] ?? 'Nationwide delivery across Nigeria for ladies wears. Home service available for beauty & wellness spa sessions.',
            'logo_url' => !empty($all['logo_path']) ? asset($all['logo_path']) : asset('images/logo.png'),
            'logo_dark_url' => !empty($all['logo_dark_path']) ? asset($all['logo_dark_path']) : asset('images/logo_dark.png'),
        ];
    }
}
