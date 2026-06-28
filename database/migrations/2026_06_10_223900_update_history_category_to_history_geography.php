<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $history = Category::where('slug', 'history')->first();
        if ($history) {
            $history->update([
                'name' => 'வரலாறு மற்றும் புவியியல்',
                'slug' => 'history-geography',
                'name_en' => 'History and Geography',
                'description' => 'வரலாற்று மற்றும் புவியியல் தகவல்கள்',
                'description_en' => 'Historical and geographical information',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $history = Category::where('slug', 'history-geography')->first();
        if ($history) {
            $history->update([
                'name' => 'வரலாறு',
                'slug' => 'history',
                'name_en' => 'History',
                'description' => 'வரலாற்று நிகழ்வுகள் மற்றும் தகவல்கள்',
                'description_en' => 'Historical events and stories',
            ]);
        }
    }
};
