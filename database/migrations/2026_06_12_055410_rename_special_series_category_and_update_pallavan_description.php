<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('categories')
            ->where('slug', 'kathaingo-special-series')
            ->update([
                'name' => 'கதைங்கோ தொடர்கள்',
                'name_en' => 'Kathaingo Series',
                'description' => 'கதைங்கோ வழங்கும் தொடர்கள்',
                'description_en' => 'Series from Kathaingo',
            ]);

        \Illuminate\Support\Facades\DB::table('series')
            ->where('slug', 'pallava-procession')
            ->update([
                'description' => 'சென்னை மாநகரத்தின் புகழ்பெற்ற "பல்லவன்" பேருந்து வழித்தடத்தில் பயணித்த நினைவுகளையும் பால்ய கால அனுபவங்களையும் பகிரும் ஒரு நினைவலைத் தொடர்.',
                'description_en' => 'A nostalgic series sharing travel experiences and childhood memories of riding on Chennai\'s legendary "Pallavan" city bus service.',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('categories')
            ->where('slug', 'kathaingo-special-series')
            ->update([
                'name' => 'கதைங்கோ சிறப்புத் தொடர்கள்',
                'name_en' => 'Kathaingo Special Series',
                'description' => 'கதைங்கோ வழங்கும் பிரத்யேக சிறப்புத் தொடர்கள்',
                'description_en' => 'Exclusive special series from Kathaingo',
            ]);

        \Illuminate\Support\Facades\DB::table('series')
            ->where('slug', 'pallava-procession')
            ->update([
                'description' => null,
                'description_en' => null,
            ]);
    }
};
