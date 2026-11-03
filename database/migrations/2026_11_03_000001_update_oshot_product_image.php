<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update product #2 or slug 'o-shot-rejuvenation-normal-repair'
        $productId = DB::table('products')->where('slug', 'o-shot-rejuvenation-normal-repair')->value('id');
        if ($productId) {
            DB::table('product_media')
                ->where('product_id', $productId)
                ->where('media_type', 'image')
                ->update(['file_path' => 'products/spa/spa_oshot_rejuvenation.jpg']);
        }

        // Also update any other record with flyer_treatment.jpg
        DB::table('product_media')
            ->where('file_path', 'like', '%flyer_treatment%')
            ->update(['file_path' => 'products/spa/spa_oshot_rejuvenation.jpg']);
    }

    public function down(): void
    {
        // No reversal needed
    }
};
