<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_root_url_sends_a_visitor_to_sign_in(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_the_login_page_renders(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_a_signed_in_user_is_sent_on_to_their_dashboard(): void
    {
        $user = User::factory()->forTenant(Tenant::factory()->create())->create();

        $this->actingAs($user)->get('/')->assertRedirect('/login');
        $this->actingAs($user)->get('/login')->assertRedirect('/dashboard');
    }
}
