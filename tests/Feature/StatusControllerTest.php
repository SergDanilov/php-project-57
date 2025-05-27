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
    public function authenticatedUserCanAccessCreateForm()
    {
        $response = $this->actingAs($this->user)
            ->get(route('task_statuses.create'));

        $response->assertStatus(200)
            ->assertViewIs('statuses.create');
    }

    #[Test]
    public function guestCannotAccessCreateForm()
    {
        $response = $this->get(route('task_statuses.create'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticatedUserCanCreateStatus()
    {
        $statusData = ['name' => 'Новый статус'];

        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), $statusData);

        $response->assertRedirect(route('task_statuses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('statuses', $statusData);
    }

    #[Test]
    public function statusCreationRequiresName()
    {
        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), ['name' => '']);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function statusNameMustBeUnique()
    {
        Status::factory()->create(['name' => 'Дубликат']);

        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), ['name' => 'Дубликат']);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function statusNameHasMax255Chars()
    {
        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), [
                'name' => str_repeat('a', 256)
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function guestCannotCreateStatus()
    {
        $response = $this->post(route('task_statuses.store'), [
            'name' => 'Гостевой статус'
        ]);

        $response->assertRedirect(route('login'));
    }
}
