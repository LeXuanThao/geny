<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test filtering users by email.
     */
    public function test_filter_users_by_email()
    {
        User::factory()->create(['email' => 'test1@example.com']);
        User::factory()->create(['email' => 'test2@example.com']);

        $response = $this->get('/users?email=test1@example.com');

        $response->assertStatus(200);
        $response->assertSee('test1@example.com');
        $response->assertDontSee('test2@example.com');
    }

    /**
     * Test filtering users by name.
     */
    public function test_filter_users_by_name()
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->get('/users?name=John');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Doe');
    }

    /**
     * Test filtering users by lockout status.
     */
    public function test_filter_users_by_lockout_status()
    {
        User::factory()->create(['email' => 'locked@example.com', 'lockout_time' => now()->addMinutes(10)]);
        User::factory()->create(['email' => 'unlocked@example.com', 'lockout_time' => null]);

        $response = $this->get('/users?lockout_status=locked');

        $response->assertStatus(200);
        $response->assertSee('locked@example.com');
        $response->assertDontSee('unlocked@example.com');
    }

    /**
     * Test pagination functionality.
     */
    public function test_pagination_functionality()
    {
        User::factory()->count(15)->create();

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Next');
        $response->assertSee('Previous');
    }

    /**
     * Test sorting users by name.
     */
    public function test_sort_users_by_name()
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->get('/users?sort=name');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Jane Doe', 'John Doe']);
    }

    /**
     * Test sorting users by email.
     */
    public function test_sort_users_by_email()
    {
        User::factory()->create(['email' => 'john@example.com']);
        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->get('/users?sort=email');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['jane@example.com', 'john@example.com']);
    }

    /**
     * Test sorting users by lockout status.
     */
    public function test_sort_users_by_lockout_status()
    {
        User::factory()->create(['email' => 'locked@example.com', 'lockout_time' => now()->addMinutes(10)]);
        User::factory()->create(['email' => 'unlocked@example.com', 'lockout_time' => null]);

        $response = $this->get('/users?sort=lockout_status');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['unlocked@example.com', 'locked@example.com']);
    }

    /**
     * Test combining filters in the query.
     */
    public function test_combining_filters_in_query()
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $response = $this->get('/users?name=John&email=john@example.com');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Doe');
    }

    /**
     * Test handling cases where filters and sorting options conflict.
     */
    public function test_handling_conflicting_filters_and_sorting_options()
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $response = $this->get('/users?name=John&sort=email');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Doe');
    }
}
