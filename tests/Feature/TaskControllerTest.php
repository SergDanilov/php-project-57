<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskControllerTest extends TestCase
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
        ->get(route('tasks.create'));

        $response->assertStatus(200)
        ->assertViewIs('tasks.create');
    }

    #[Test]
    public function guestCannotAccessCreateForm()
    {
        $response = $this->get(route('tasks.create'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticatedUserCanCreateTask()
    {
        $status = Status::factory()->create();
        $assignee = User::factory()->create();

        $taskData = [
                'name' => 'Новая задача',
                'status_id' => $status->id,
                'assigned_to_id' => $assignee->id,
                'created_by_id' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
                ->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', $taskData);
    }

    #[Test]
    public function taskCreationRequiresName()
    {
        $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), ['name' => '']);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]

    public function taskNameMustBeUnique()
    {
        $status = Status::factory()->create();
        $assignee = User::factory()->create();

        Task::factory()->create([
                'name' => 'Дубликат',
                'status_id' => $status->id,
                'assigned_to_id' => $assignee->id,
                'created_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
                ->post(route('tasks.store'), [
                'name' => 'Дубликат',
                'status_id' => $status->id,
                'assigned_to_id' => $assignee->id,
                ]);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]

    public function taskNameHasMax255Chars()
    {
        $response = $this->actingAs($this->user)
        ->post(route('tasks.store'), [
                'name' => str_repeat('a', 256)
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]

    public function guestCannotCreateTask()
    {
        $response = $this->post(route('tasks.store'), [
        'name' => 'Гостевая задача'
        ]);

        $response->assertRedirect(route('login'));
    }
}
