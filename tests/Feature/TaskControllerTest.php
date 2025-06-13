<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Label;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected User $assignee;
    protected TaskStatus $status;
    protected Label $label;
    protected Task $task;
    protected array $taskData;
    protected array $duplicateTaskData;
    protected array $longNameData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->assignee = User::factory()->create();
        $this->status = TaskStatus::factory()->create();
        $this->label = Label::factory()->create();

        $this->task = Task::factory()->create([
            'name' => 'Тестовая задача',
            'status_id' => $this->status->id,
            'assigned_to_id' => $this->assignee->id,
            'created_by_id' => $this->user->id,
        ]);
        $this->task->labels()->attach($this->label);

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

    public function testIndexPageDisplaysTasks()
    {
        $response = $this->get(route('tasks.index'));

        $response->assertOk()
            ->assertViewIs('tasks.index')
            ->assertSee($this->task->name)
            ->assertViewHas('tasks')
            ->assertViewHas('statuses')
            ->assertViewHas('users')
            ->assertViewHas('labels');
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

        public function testAuthenticatedUserCanUpdateTask()
    {
        $updatedData = [
            'name' => 'Обновленная задача',
            'status_id' => $this->status->id,
            'assigned_to_id' => $this->assignee->id,
            'labels' => [$this->label->id]
        ];

        $response = $this->actingAs($this->user)
            ->put(route('tasks.update', $this->task), $updatedData);

        $response->assertRedirect(route('tasks.index'))
            ->assertSessionDoesntHaveErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'name' => 'Обновленная задача'
        ]);
    }

    public function testAuthenticatedUserCanDeleteTask()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('tasks.destroy', $this->task));

        $response->assertRedirect(route('tasks.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tasks', ['id' => $this->task->id]);
    }

    public function testGuestCannotDeleteTask()
    {
        $response = $this->delete(route('tasks.destroy', $this->task));
        $response->assertRedirect(route('login'));
    }

    public function testUnauthorizedUserCannotDeleteTask()
    {
        $otherUser = User::factory()->create();
        $response = $this->actingAs($otherUser)
            ->delete(route('tasks.destroy', $this->task));

        $response->assertForbidden();
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
