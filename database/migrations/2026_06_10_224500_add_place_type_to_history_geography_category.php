<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\MetadataType;
use App\Models\MetadataValue;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Setup Place Type for History and Geography Category
        $historyGeo = Category::where('slug', 'history-geography')->first();
        if ($historyGeo) {
            $placeType = MetadataType::firstOrCreate(
                ['category_id' => $historyGeo->id, 'slug' => 'place-type'],
                [
                    'name' => 'இடத்தின் வகை',
                    'name_en' => 'Place Type',
                    'is_hierarchical' => false,
                ]
            );

            $values = [
                ['name' => 'நகரம்', 'slug' => 'city', 'name_en' => 'City'],
                ['name' => 'கிராமம்', 'slug' => 'village', 'name_en' => 'Village'],
                ['name' => 'மலை', 'slug' => 'mountain', 'name_en' => 'Mountain'],
                ['name' => 'கடற்கரை', 'slug' => 'beach', 'name_en' => 'Beach'],
                ['name' => 'தீவு', 'slug' => 'island', 'name_en' => 'Island'],
                ['name' => 'கோவில்', 'slug' => 'temple', 'name_en' => 'Temple'],
                ['name' => 'அரண்மனை', 'slug' => 'palace', 'name_en' => 'Palace'],
                ['name' => 'கோட்டை', 'slug' => 'fort', 'name_en' => 'Fort'],
                ['name' => 'அருங்காட்சியகம்', 'slug' => 'museum', 'name_en' => 'Museum'],
                ['name' => 'இயற்கை', 'slug' => 'nature', 'name_en' => 'Nature'],
                ['name' => 'ஏரி', 'slug' => 'lake', 'name_en' => 'Lake'],
                ['name' => 'ஆறு', 'slug' => 'river', 'name_en' => 'River'],
            ];

            foreach ($values as $val) {
                MetadataValue::firstOrCreate(
                    ['metadata_type_id' => $placeType->id, 'slug' => $val['slug']],
                    [
                        'name' => $val['name'],
                        'name_en' => $val['name_en'],
                    ]
                );
            }
        }

        // 2. Add Lake & River to Travel Category's Place Type
        $travel = Category::where('slug', 'travel')->first();
        if ($travel) {
            $travelPlaceType = MetadataType::where('category_id', $travel->id)
                ->where('slug', 'place-type')
                ->first();

            if ($travelPlaceType) {
                $additionalValues = [
                    ['name' => 'ஏரி', 'slug' => 'lake', 'name_en' => 'Lake'],
                    ['name' => 'ஆறு', 'slug' => 'river', 'name_en' => 'River'],
                ];

                foreach ($additionalValues as $val) {
                    MetadataValue::firstOrCreate(
                        ['metadata_type_id' => $travelPlaceType->id, 'slug' => $val['slug']],
                        [
                            'name' => $val['name'],
                            'name_en' => $val['name_en'],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $historyGeo = Category::where('slug', 'history-geography')->first();
        if ($historyGeo) {
            $placeType = MetadataType::where('category_id', $historyGeo->id)
                ->where('slug', 'place-type')
                ->first();

            if ($placeType) {
                $placeType->delete();
            }
        }

        $travel = Category::where('slug', 'travel')->first();
        if ($travel) {
            $travelPlaceType = MetadataType::where('category_id', $travel->id)
                ->where('slug', 'place-type')
                ->first();

            if ($travelPlaceType) {
                MetadataValue::where('metadata_type_id', $travelPlaceType->id)
                    ->whereIn('slug', ['lake', 'river'])
                    ->delete();
            }
        }
    }
};
