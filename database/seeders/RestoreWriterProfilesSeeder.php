<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * RestoreWriterProfilesSeeder
 *
 * Restores all writer/author subcategory profiles under the 'pathivargal' category.
 * Run this after a database wipe: php artisan db:seed --class=RestoreWriterProfilesSeeder
 */
class RestoreWriterProfilesSeeder extends Seeder
{
    public function run(): void
    {
        $writersCategory = Category::where('slug', 'pathivargal')->first();

        if (!$writersCategory) {
            $this->command->error('Writers category (pathivargal) not found. Run TaxonomySeeder first.');
            return;
        }

        // Core system author profiles (special/anonymous types)
        $systemProfiles = [
            [
                'name'           => 'Face Book Post',
                'slug'           => 'face-book-post',
                'name_en'        => 'Face Book Post',
                'description'    => 'Face Book Post',
                'description_en' => 'Face Book Post',
                'tagline'        => 'முகநூல் பக்கங்களில் பகிரப்பட்ட சுவாரசியமான பதிவுகள்',
                'tagline_en'     => 'Interesting posts shared on Facebook pages',
                'trust_level'    => 1,
                'is_featured'    => false,
            ],
            [
                'name'           => 'Whatsapp Forward',
                'slug'           => 'whatsapp-forward',
                'name_en'        => 'Whatsapp Forward',
                'description'    => 'Whatsapp Forward',
                'description_en' => 'Whatsapp Forward',
                'tagline'        => 'வாட்ஸ்அப்பில் பகிரப்பட்டு வரும் பயனுள்ள தகவல்கள்',
                'tagline_en'     => 'Useful forwards shared on WhatsApp',
                'trust_level'    => 1,
                'is_featured'    => false,
            ],
            [
                'name'           => 'யாரோ (Anonymous)',
                'slug'           => 'yaro-anonymous',
                'name_en'        => 'Anonymous',
                'description'    => 'யாரோ (Anonymous)',
                'description_en' => 'Anonymous',
                'tagline'        => 'பகிரப்படாத முகங்கள், பகிரப்படும் கருத்துக்கள்',
                'tagline_en'     => 'Unshared faces, shared thoughts',
                'trust_level'    => 1,
                'is_featured'    => false,
            ],
            [
                'name'           => 'படித்ததில் பிடித்தது',
                'slug'           => 'padithathil-pidithathu',
                'name_en'        => 'Favorite Reads',
                'description'    => 'படித்ததில் பிடித்தது',
                'description_en' => 'Favorite Reads',
                'tagline'        => 'படித்ததில் கவர்ந்த சிறந்த படைப்புகள்',
                'tagline_en'     => 'Handpicked reads that inspired us',
                'trust_level'    => 2,
                'is_featured'    => false,
            ],
        ];

        foreach ($systemProfiles as $profile) {
            Subcategory::updateOrCreate(
                ['category_id' => $writersCategory->id, 'slug' => $profile['slug']],
                [
                    'name'           => $profile['name'],
                    'name_en'        => $profile['name_en'],
                    'description'    => $profile['description'],
                    'description_en' => $profile['description_en'],
                    'tagline'        => $profile['tagline'],
                    'tagline_en'     => $profile['tagline_en'],
                    'trust_level'    => $profile['trust_level'],
                    'is_featured'    => $profile['is_featured'],
                    'order'          => 1,
                ]
            );
            $this->command->info("  ✓ Writer profile: {$profile['name']}");
        }

        // Link admin user to an author profile
        $adminUser = User::where('email', 'admin@kathaingo.com')->first();
        if ($adminUser) {
            $adminProfile = Subcategory::updateOrCreate(
                ['category_id' => $writersCategory->id, 'slug' => 'admin-kathaingo'],
                [
                    'name'           => 'கதைங்கோ குழு',
                    'name_en'        => 'Kathaingo Team',
                    'description'    => 'கதைங்கோ ஆசிரியர் குழு',
                    'description_en' => 'Kathaingo editorial team',
                    'tagline'        => 'கதைங்கோவின் அதிகாரப்பூர்வ ஆசிரியர் குழு',
                    'tagline_en'     => 'Official editorial team of Kathaingo',
                    'trust_level'    => 3,
                    'is_featured'    => true,
                    'order'          => 0,
                    'user_id'        => $adminUser->id,
                ]
            );
            $this->command->info("  ✓ Admin profile linked: கதைங்கோ குழு (id: {$adminProfile->id})");
        }

        $total = Subcategory::where('category_id', $writersCategory->id)->count();
        $this->command->info("\n✅ Writer profiles restored: {$total} profiles under 'pathivargal'");
    }
}
