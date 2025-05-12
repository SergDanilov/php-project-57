<?php

namespace Tests\Feature;

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatusControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function authenticated_user_can_access_create_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('task_statuses.create'));

        $response->assertStatus(200)
            ->assertViewIs('statuses.create');
    }

    #[Test]
    public function guest_cannot_access_create_form()
    {
        $response = $this->get(route('task_statuses.create'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_create_status()
    {
        $statusData = ['name' => 'Новый статус'];

        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), $statusData);

        $response->assertRedirect(route('task_statuses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('statuses', $statusData);
    }

    #[Test]
    public function status_creation_requires_name()
    {
        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), ['name' => '']);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function status_name_must_be_unique()
    {
        Status::factory()->create(['name' => 'Дубликат']);

        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), ['name' => 'Дубликат']);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function status_name_has_max_255_chars()
    {
        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), [
                'name' => str_repeat('a', 256)
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function guest_cannot_create_status()
    {
        $response = $this->post(route('task_statuses.store'), [
            'name' => 'Гостевой статус'
        ]);

        $response->assertRedirect(route('login'));
    }
}