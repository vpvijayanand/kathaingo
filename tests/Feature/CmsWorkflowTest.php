<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\LoginActivity;
use App\Models\PostRevision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_requires_valid_captcha()
    {
        $this->get('/register');
        $correctCaptcha = session('captcha_answer');

        // Submit registration with invalid captcha
        $response = $this->post('/register', [
            'name' => 'Test Contributor',
            'email' => 'contributor@example.com',
            'mobile' => '1234567890',
            'gender' => 'male',
            'location' => 'Chennai',
            'dob' => '1990-01-01',
            'password' => 'SecurPass12!',
            'password_confirmation' => 'SecurPass12!',
            'captcha_answer' => 'wrong',
        ]);

        $response->assertSessionHasErrors('captcha_answer');
        $this->assertDatabaseMissing('users', ['email' => 'contributor@example.com']);

        // Submit registration with valid captcha
        session(['captcha_answer' => $correctCaptcha]);
        $response2 = $this->post('/register', [
            'name' => 'Test Contributor',
            'email' => 'contributor@example.com',
            'mobile' => '1234567890',
            'gender' => 'male',
            'location' => 'Chennai',
            'dob' => '1990-01-01',
            'password' => 'SecurPass12!',
            'password_confirmation' => 'SecurPass12!',
            'captcha_answer' => $correctCaptcha,
        ]);

        $response2->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'contributor@example.com']);
    }

    public function test_registration_enforces_strong_password_policy()
    {
        $correctCaptcha = 'test-captcha';
        session(['captcha_answer' => $correctCaptcha]);

        // 1. Weak password (common word)
        $response = $this->post('/register', [
            'name' => 'Test Contributor',
            'email' => 'contributor@example.com',
            'mobile' => '1234567890',
            'gender' => 'male',
            'password' => 'password',
            'password_confirmation' => 'password',
            'captcha_answer' => $correctCaptcha,
        ]);
        $response->assertSessionHasErrors('password');

        // 2. Too short (<8 chars)
        session(['captcha_answer' => $correctCaptcha]);
        $response2 = $this->post('/register', [
            'name' => 'Test Contributor',
            'email' => 'contributor@example.com',
            'mobile' => '1234567890',
            'gender' => 'male',
            'password' => 'Sh1t!',
            'password_confirmation' => 'Sh1t!',
            'captcha_answer' => $correctCaptcha,
        ]);
        $response2->assertSessionHasErrors('password');

        // 3. Too long (>12 chars)
        session(['captcha_answer' => $correctCaptcha]);
        $response3 = $this->post('/register', [
            'name' => 'Test Contributor',
            'email' => 'contributor@example.com',
            'mobile' => '1234567890',
            'gender' => 'male',
            'password' => 'VeryLongSecurePass123!',
            'password_confirmation' => 'VeryLongSecurePass123!',
            'captcha_answer' => $correctCaptcha,
        ]);
        $response3->assertSessionHasErrors('password');
    }

    public function test_login_attempts_are_logged_to_audit_trail()
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => bcrypt('SecurePass123!'),
            'role' => 'author',
            'is_approved' => true,
        ]);

        // Failed attempt
        $this->post('/login', [
            'email' => 'member@example.com',
            'password' => 'wrongpass',
        ]);

        $this->assertDatabaseHas('login_activities', [
            'email' => 'member@example.com',
            'is_successful' => false,
        ]);

        // Successful attempt
        $this->post('/login', [
            'email' => 'member@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->assertDatabaseHas('login_activities', [
            'user_id' => $user->id,
            'is_successful' => true,
        ]);
    }

    public function test_author_cannot_directly_publish_posts()
    {
        $author = User::factory()->create([
            'role' => 'author',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($author);

        // Try to save direct published post
        $response = $this->post('/posts', [
            'title' => 'Author Post Title',
            'content' => 'Content of author post.',
            'status' => 'published',
        ]);

        $post = Post::first();
        // Should fallback to draft or submitted
        $this->assertNotEquals('published', $post->status);
    }

    public function test_editor_can_publish_posts_and_log_revisions()
    {
        $author = User::factory()->create([
            'role' => 'author',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $editor = User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $post = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original Content',
            'author_id' => $author->id,
            'status' => 'submitted',
        ]);

        $this->actingAs($editor);

        // Editor reviews and updates content + status to published
        $response = $this->put("/posts/{$post->id}", [
            'title' => 'Edited Title',
            'content' => 'Edited Content',
            'status' => 'published',
        ]);

        $post->refresh();
        $this->assertEquals('published', $post->status);
        $this->assertEquals('Edited Title', $post->title);

        // Check if revision was created with original content
        $this->assertDatabaseHas('post_revisions', [
            'post_id' => $post->id,
            'user_id' => $editor->id,
            'title' => 'Original Title',
            'content' => 'Original Content',
        ]);
    }

    public function test_author_can_edit_own_published_post()
    {
        $author = User::factory()->create([
            'role' => 'author',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $post = Post::create([
            'title' => 'Published Title',
            'slug' => 'published-title',
            'content' => 'Published Content',
            'author_id' => $author->id,
            'status' => 'published',
        ]);

        $this->actingAs($author);

        // Edit and set status to submitted
        $response = $this->put("/posts/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'status' => 'submitted',
        ]);

        $response->assertRedirect();
        $post->refresh();
        $this->assertEquals('Updated Title', $post->title);
        $this->assertEquals('submitted', $post->status);
    }

    public function test_admin_can_filter_posts_by_status()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $postSubmitted = Post::create([
            'title' => 'Submitted Post',
            'slug' => 'submitted-post',
            'content' => 'Content here',
            'author_id' => $admin->id,
            'status' => 'submitted',
        ]);

        $postPublished = Post::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'content' => 'Content here',
            'author_id' => $admin->id,
            'status' => 'published',
        ]);

        $this->actingAs($admin);

        // Fetch posts filtered by submitted status
        $response = $this->get('/posts?status=submitted');

        $response->assertStatus(200);
        $response->assertSee('Submitted Post');
        $response->assertDontSee('Published Post');
    }
}

