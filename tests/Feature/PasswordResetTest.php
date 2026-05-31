<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the forgot password view is accessible.
     */
    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');
    }

    /**
     * Test requesting a password reset link.
     */
    public function test_user_can_request_reset_link()
    {
        $user = User::factory()->create([
            'email' => 'testmember@kdkmp.org',
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'testmember@kdkmp.org',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $response->assertSessionHas('simulated_link');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'testmember@kdkmp.org',
        ]);
    }

    /**
     * Test requesting password reset with non-existent email fails.
     */
    public function test_non_existent_email_cannot_request_reset()
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@kdkmp.org',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'nonexistent@kdkmp.org',
        ]);
    }

    /**
     * Test reset password form page is accessible.
     */
    public function test_reset_password_page_is_accessible()
    {
        $email = 'testmember@kdkmp.org';
        $token = 'randomtesttoken123';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $email]));
        $response->assertStatus(200);
        $response->assertSee('Atur Ulang Sandi');
    }

    /**
     * Test resetting password.
     */
    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'testmember@kdkmp.org',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = 'validresettoken123';

        DB::table('password_reset_tokens')->insert([
            'email' => 'testmember@kdkmp.org',
            'token' => $token,
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'testmember@kdkmp.org',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Check password is updated in DB
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));

        // Check token is deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'testmember@kdkmp.org',
        ]);
    }
}
