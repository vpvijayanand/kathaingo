<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $writersCategory = Category::where('slug', 'pathivargal')->first();
        if ($writersCategory) {
            $profiles = [
                [
                    'name' => 'Face Book Post',
                    'slug' => 'face-book-post',
                    'name_en' => 'Face Book Post',
                    'description' => 'Face Book Post',
                    'description_en' => 'Face Book Post',
                ],
                [
                    'name' => 'Whatsapp Forward',
                    'slug' => 'whatsapp-forward',
                    'name_en' => 'Whatsapp Forward',
                    'description' => 'Whatsapp Forward',
                    'description_en' => 'Whatsapp Forward',
                ],
                [
                    'name' => 'யாரோ (Anonymous)',
                    'slug' => 'yaro-anonymous',
                    'name_en' => 'Anonymous',
                    'description' => 'யாரோ (Anonymous)',
                    'description_en' => 'Anonymous',
                ],
                [
                    'name' => 'படித்ததில் பிடித்தது',
                    'slug' => 'padithathil-pidithathu',
                    'name_en' => 'Favorite Reads',
                    'description' => 'படித்ததில் பிடித்தது',
                    'description_en' => 'Favorite Reads',
                ]
            ];

            foreach ($profiles as $profile) {
                Subcategory::updateOrCreate(
                    [
                        'category_id' => $writersCategory->id,
                        'slug' => $profile['slug']
                    ],
                    [
                        'name' => $profile['name'],
                        'name_en' => $profile['name_en'],
                        'description' => $profile['description'],
                        'description_en' => $profile['description_en'],
                        'order' => 1
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $writersCategory = Category::where('slug', 'pathivargal')->first();
        if ($writersCategory) {
            Subcategory::where('category_id', $writersCategory->id)
                ->whereIn('slug', ['face-book-post', 'whatsapp-forward', 'yaro-anonymous', 'padithathil-pidithathu'])
                ->delete();
        }
    }
};
