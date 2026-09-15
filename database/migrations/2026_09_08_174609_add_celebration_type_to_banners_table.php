<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('hero','promo_strip','occasion_reminder','app_deal','celebration','other') DEFAULT 'hero'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('hero','promo_strip','occasion_reminder','app_deal','other') DEFAULT 'hero'");
    }
};