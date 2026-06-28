<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $correctCaptcha = 'test-captcha';
        session(['captcha_answer' => $correctCaptcha]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'mobile' => '1234567890',
            'gender' => 'male',
            'password' => 'SecurPass12!',
            'password_confirmation' => 'SecurPass12!',
            'captcha_answer' => $correctCaptcha,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
