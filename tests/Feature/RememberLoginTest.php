<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RememberLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_remember_me_sets_remember_token(): void
    {
        $user = User::factory()->create([
            'login' => 'remember-user',
            'password' => 'secret-password',
            'remember_token' => null,
        ]);

        $this
            ->post(route('login'), [
                'login' => 'remember-user',
                'password' => 'secret-password',
                'remember' => '1',
            ])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_login_without_remember_me_keeps_remember_token_empty(): void
    {
        $user = User::factory()->create([
            'login' => 'session-user',
            'password' => 'secret-password',
            'remember_token' => null,
        ]);

        $this
            ->post(route('login'), [
                'login' => 'session-user',
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->fresh()->remember_token);
    }
}
