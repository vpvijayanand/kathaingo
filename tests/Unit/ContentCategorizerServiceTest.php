<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ContentCategorizerService;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentCategorizerServiceTest extends TestCase
{
    use RefreshDatabase;

    private ContentCategorizerService $categorizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categorizer = new ContentCategorizerService();

        // Seed Category: Posts (பதிவுகள்)
        $postsCat = Category::create([
            'name' => 'Posts',
            'slug' => 'pathivugal',
            'order' => 0
        ]);

        // Seed Subcategory: Good Cinema (நல்லசினிமா)
        $cinemaSub = Subcategory::create([
            'category_id' => $postsCat->id,
            'name' => 'Good Cinema',
            'slug' => 'நல்லசினிமா',
            'order' => 1
        ]);

        // Seed Subcategory: Travels (பயணங்கள்)
        $travelSub = Subcategory::create([
            'category_id' => $postsCat->id,
            'name' => 'Travels',
            'slug' => 'payanangal',
            'order' => 2
        ]);

        // Seed Child Category Travels: Country (நாடு)
        $countryChild = ChildCategory::create([
            'subcategory_id' => $travelSub->id,
            'name' => 'Country',
            'slug' => 'நாடு',
            'order' => 1
        ]);

        // Seed Child Category Travels: Tourism (சுற்றுலா)
        ChildCategory::create([
            'subcategory_id' => $travelSub->id,
            'name' => 'Tourism',
            'slug' => 'சுற்றுலா',
            'order' => 2
        ]);

        // Seed Child Category Travels: Solo Travel (தனிப் பயணம்)
        ChildCategory::create([
            'subcategory_id' => $travelSub->id,
            'name' => 'Solo Travel',
            'slug' => 'தனிப்-பயணம்',
            'order' => 3
        ]);

        // Seed Child Category Travels: Travel Experience (பயண அனுபவம்)
        ChildCategory::create([
            'subcategory_id' => $travelSub->id,
            'name' => 'Travel Experience',
            'slug' => 'பயண-அனுபவம்',
            'order' => 4
        ]);

        // Seed Child Category Travels: Adventures (சாகசங்கள்)
        ChildCategory::create([
            'subcategory_id' => $travelSub->id,
            'name' => 'Adventures',
            'slug' => 'சாகசங்கள்',
            'order' => 5
        ]);



        // Seed Child Categories (தமிழ், ஆங்கிலம்)
        $tamilChild = ChildCategory::create([
            'subcategory_id' => $cinemaSub->id,
            'name' => 'Tamil',
            'slug' => 'தமிழ்',
            'order' => 1
        ]);

        $englishChild = ChildCategory::create([
            'subcategory_id' => $cinemaSub->id,
            'name' => 'English',
            'slug' => 'ஆங்கிலம்',
            'order' => 2
        ]);

        // Seed Grandchild Categories
        GrandchildCategory::create([
            'child_category_id' => $tamilChild->id,
            'name' => 'Family/Drama',
            'slug' => 'தமிழ்-குடும்ப-நாடகம்',
            'order' => 1
        ]);

        GrandchildCategory::create([
            'child_category_id' => $englishChild->id,
            'name' => 'Horror',
            'slug' => 'ஆங்கிலம்-அமானுஷ்யம்',
            'order' => 1
        ]);
    }

    public function test_categorizes_travel_content_correctly()
    {
        // 1. Test Travel Experience
        $title = "காஷ்மீர் பயண அனுபவம்";
        $content = "நாங்கள் விமானம் மூலம் ஸ்ரீநகர் சென்றடைந்தோம். தால் ஏரி மற்றும் அழகான மலைகள் நிறைந்த ஒரு நல்ல பயணக் கட்டுரை.";

        $result = $this->categorizer->categorize($title, $content);
        $postsCat = Category::where('slug', 'pathivugal')->first();
        $travelSub = Subcategory::where('slug', 'payanangal')->first();
        $experienceChild = ChildCategory::where('slug', 'பயண-அனுபவம்')->first();

        $this->assertEquals($postsCat->id, $result['category_id']);
        $this->assertEquals($travelSub->id, $result['subcategory_id']);
        $this->assertEquals($experienceChild->id, $result['child_category_id']);
        $this->assertNull($result['grandchild_category_id']);

        // 2. Test Solo Travel
        $title = "இமயமலை தனிப் பயணம்";
        $content = "தனியாக பேக்பேக் எடுத்துக்கொண்டு இமயமலையில் தனிப்பயணம் மேற்கொண்டேன்.";
        $result = $this->categorizer->categorize($title, $content);
        $soloChild = ChildCategory::where('slug', 'தனிப்-பயணம்')->first();
        $this->assertEquals($soloChild->id, $result['child_category_id']);

        // 3. Test Adventure
        $title = "காடு மலையேற்றம் சாகசங்கள்";
        $content = "அடர்ந்த காட்டில் மலையேற்றம் செய்து புதிய சாகசங்களை எதிர்கொண்டோம். trekking and hiking trip.";
        $result = $this->categorizer->categorize($title, $content);
        $adventureChild = ChildCategory::where('slug', 'சாகசங்கள்')->first();
        $this->assertEquals($adventureChild->id, $result['child_category_id']);

        // 4. Test default/tourism
        $title = "சுற்றுலாத்தலம் வழிகாட்டி";
        $content = "அழகான சுற்றுலாத் தலங்கள் மற்றும் சிறந்த தங்கும் விடுதிகள் பற்றிய சுற்றுலா வழிகாட்டி.";
        $result = $this->categorizer->categorize($title, $content);
        $tourismChild = ChildCategory::where('slug', 'சுற்றுலா')->first();
        $this->assertEquals($tourismChild->id, $result['child_category_id']);
    }

    public function test_categorizes_cinema_tamil_romance_content_correctly()
    {
        $title = "காதல் மற்றும் குடும்பத் திரைப்படம் விமர்சனம்";
        $content = "இயக்குநர் மணிரத்னம் இயக்கிய இந்தப் படம் ஒரு அழகான தமிழ் காதல் மற்றும் குடும்ப நாடகம் ஆகும். சிறந்த பாசம் மற்றும் பாடல்கள் உள்ளன.";

        $result = $this->categorizer->categorize($title, $content);

        // Fetch expected IDs
        $postsCat = Category::where('slug', 'pathivugal')->first();
        $cinemaSub = Subcategory::where('slug', 'நல்லசினிமா')->first();
        $tamilChild = ChildCategory::where('slug', 'தமிழ்')->first();
        $familyGrandchild = GrandchildCategory::where('slug', 'தமிழ்-குடும்ப-நாடகம்')->first();

        $this->assertEquals($postsCat->id, $result['category_id']); // Category: Posts
        $this->assertEquals($cinemaSub->id, $result['subcategory_id']); // Subcategory: Good Cinema
        $this->assertEquals($tamilChild->id, $result['child_category_id']); // Child: Tamil
        $this->assertEquals($familyGrandchild->id, $result['grandchild_category_id']); // Grandchild: Family/Drama
    }

    public function test_categorizes_cinema_english_horror_content_correctly()
    {
        $title = "New Hollywood Horror Review";
        $content = "This english movie is a terrifying horror film with ghosts and supernatural fear. The director created paranormal suspense.";

        $result = $this->categorizer->categorize($title, $content);

        // Fetch expected IDs
        $postsCat = Category::where('slug', 'pathivugal')->first();
        $cinemaSub = Subcategory::where('slug', 'நல்லசினிமா')->first();
        $englishChild = ChildCategory::where('slug', 'ஆங்கிலம்')->first();
        $horrorGrandchild = GrandchildCategory::where('slug', 'ஆங்கிலம்-அமானுஷ்யம்')->first();

        $this->assertEquals($postsCat->id, $result['category_id']); // Category: Posts
        $this->assertEquals($cinemaSub->id, $result['subcategory_id']); // Subcategory: Good Cinema
        $this->assertEquals($englishChild->id, $result['child_category_id']); // Child: English
        $this->assertEquals($horrorGrandchild->id, $result['grandchild_category_id']); // Grandchild: English Horror
    }
}
