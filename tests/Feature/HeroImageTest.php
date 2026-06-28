<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\HeroImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroImageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true
        ]);

        $this->regularUser = User::factory()->create([
            'is_admin' => false,
            'is_approved' => true
        ]);
    }

    public function test_admin_can_access_hero_images_index_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.hero-images.index'));
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_hero_images_index_page(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.hero-images.index'));
        $response->assertStatus(403); // assuming there's admin middleware or gates restricting it
    }

    public function test_admin_can_upload_new_hero_image_via_file(): void
    {
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->image('hero.jpg');

        $response = $this->post(route('admin.hero-images.store'), [
            'image' => $file,
        ]);

        $response->assertRedirect(route('admin.hero-images.index'));
        $response->assertSessionHas('success', 'Image uploaded successfully.');

        $this->assertDatabaseHas('hero_images', [
            'order' => 1,
        ]);

        $heroImage = HeroImage::first();
        $this->assertNotNull($heroImage);
        
        // Assert storage has file
        $path = str_replace('/storage/', '', $heroImage->image_path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_upload_new_hero_image_via_base64_cropping(): void
    {
        $this->actingAs($this->admin);

        // A fake 1x1 red pixel JPEG base64 Data URL
        $base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->post(route('admin.hero-images.store'), [
            'cropped_image' => $base64Image,
        ]);

        $response->assertRedirect(route('admin.hero-images.index'));
        $response->assertSessionHas('success', 'Image cropped and uploaded successfully.');

        $this->assertDatabaseHas('hero_images', [
            'order' => 1,
        ]);

        $heroImage = HeroImage::first();
        $this->assertNotNull($heroImage);

        $path = str_replace('/storage/', '', $heroImage->image_path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_reorder_hero_images_via_ajax(): void
    {
        $this->actingAs($this->admin);

        $image1 = HeroImage::create([
            'image_path' => '/storage/hero-images/1.jpg',
            'order' => 1,
        ]);

        $image2 = HeroImage::create([
            'image_path' => '/storage/hero-images/2.jpg',
            'order' => 2,
        ]);

        $response = $this->postJson(route('admin.hero-images.reorder'), [
            'ids' => [$image2->id, $image1->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, $image2->fresh()->order);
        $this->assertEquals(2, $image1->fresh()->order);
    }

    public function test_regular_user_cannot_reorder_hero_images(): void
    {
        $this->actingAs($this->regularUser);

        $image1 = HeroImage::create([
            'image_path' => '/storage/hero-images/1.jpg',
            'order' => 1,
        ]);

        $image2 = HeroImage::create([
            'image_path' => '/storage/hero-images/2.jpg',
            'order' => 2,
        ]);

        $response = $this->postJson(route('admin.hero-images.reorder'), [
            'ids' => [$image2->id, $image1->id],
        ]);

        $response->assertStatus(403);
    }
}
