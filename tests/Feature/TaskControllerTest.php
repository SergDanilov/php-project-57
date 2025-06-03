<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected User $assignee;
    protected Status $status;
    protected array $taskData;
    protected array $duplicateTaskData;
    protected array $longNameData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->assignee = User::factory()->create();
        $this->status = Status::factory()->create();

        // основные тестовые данные задачи
        $this->taskData = [
            'name' => 'Новая задача',
            'status_id' => $this->status->id,
            'assigned_to_id' => $this->assignee->id,
            'created_by_id' => $this->user->id,
        ];

        // для проверки дублирования
        $this->duplicateTaskData = [
            'name' => 'Дубликат задачи',
            'status_id' => $this->status->id,
            'assigned_to_id' => $this->assignee->id,
        ];

        // для проверки длины имени
        $this->longNameData = [
            'name' => str_repeat('a', 256),
            'status_id' => $this->status->id,
            'assigned_to_id' => $this->assignee->id,
        ];

        // дубликат для теста уникальности
        Task::factory()->create([
            'name' => $this->duplicateTaskData['name'],
            'status_id' => $this->status->id,
            'assigned_to_id' => $this->assignee->id,
            'created_by_id' => $this->user->id,
        ]);
    }

    public function testAuthenticatedUserCanAccessCreateForm()
    {
        $response = $this->actingAs($this->user)
            ->get(route('tasks.create'));

        $response->assertOk()
            ->assertViewIs('tasks.create');
    }

    public function testGuestCannotAccessCreateForm()
    {
        $response = $this->get(route('tasks.create'));
        $response->assertRedirect(route('login'));
    }

    public function testAuthenticatedUserCanCreateTask()
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $this->taskData);

        $response->assertRedirect(route('tasks.index'))
            ->assertSessionDoesntHaveErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', $this->taskData);
    }

    public function testTaskCreationRequiresName()
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'name' => '',
                'status_id' => $this->status->id,
                'assigned_to_id' => $this->assignee->id,
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function testTaskNameMustBeUnique()
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $this->duplicateTaskData);

        $response->assertSessionHasErrors(['name']);
    }

    public function testTaskNameHasMax255Chars()
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $this->longNameData);

        $response->assertSessionHasErrors(['name']);
    }

    public function testGuestCannotCreateTask()
    {
        $response = $this->post(route('tasks.store'), [
            'name' => 'Гостевая задача',
            'status_id' => $this->status->id,
            'assigned_to_id' => $this->assignee->id,
        ]);

        $response->assertRedirect(route('login'));
    }
}
