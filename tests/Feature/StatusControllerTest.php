<?php

namespace Tests\Feature;

use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatusControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected TaskStatus $status;
    protected array $statusData;
    protected array $duplicateStatusData;
    protected array $longNameData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // Создаем статус с тем же именем, что и в duplicateStatusData
        $this->duplicateStatusData = ['name' => 'Дубликат'];
        $this->status = TaskStatus::factory()->create(['name' => $this->duplicateStatusData['name']]);

        // Основные тестовые данные
        $this->statusData = [
            'name' => 'Тестовый статус'
        ];

        // Данные для проверки длины
        $this->longNameData = [
            'name' => str_repeat('a', 256)
        ];
    }

    public function testAuthenticatedUserCanAccessCreateForm()
    {
        $response = $this->actingAs($this->user)
            ->get(route('task_statuses.create'));

        $response->assertOk()
            ->assertViewIs('statuses.create');
    }

    public function testGuestCannotAccessCreateForm()
    {
        $response = $this->get(route('task_statuses.create'));

        $response->assertRedirect(route('login'));
    }

    public function testAuthenticatedUserCanCreateStatus()
    {

        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), $this->statusData);

        $response->assertRedirect(route('task_statuses.index'))
            ->assertSessionDoesntHaveErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_statuses', $this->statusData);
    }

    public function testGuestCannotCreateStatus()
    {
        $response = $this->post(route('task_statuses.store'), $this->statusData);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('task_statuses', $this->statusData);
    }

    public function testAuthenticatedUserCanAccessEditForm()
    {
        $response = $this->actingAs($this->user)
            ->get(route('task_statuses.edit', $this->status));

        $response->assertOk()
            ->assertViewIs('statuses.edit')
            ->assertViewHas('task_status', $this->status);
    }

    public function testGuestCannotAccessEditForm()
    {
        $response = $this->get(route('task_statuses.edit', $this->status));
        $response->assertRedirect(route('login'));
    }

    public function testCreatorCanDeleteStatus()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('task_statuses.destroy', $this->status));

        $response->assertRedirect(route('task_statuses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('task_statuses', ['id' => $this->status->id]);
    }

    public function testGuestCannotDeleteStatus()
    {
        $response = $this->delete(route('task_statuses.destroy', $this->status));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('task_statuses', ['id' => $this->status->id]);
    }

    public function testCannotDeleteStatusWithAssociatedTasks()
    {
        // Создаем задачу с этим статусом
        $task = Task::factory()->create([
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id
        ]);

        // Пытаемся удалить статус
        $response = $this->actingAs($this->user)
            ->delete(route('task_statuses.destroy', $this->status));

        $response->assertRedirect()
            ->assertSessionHas('error', __('messages.status__cannot__be__deleted'));

        $this->assertDatabaseHas('task_statuses', ['id' => $this->status->id]);
        $this->assertDatabaseHas('tasks', ['status_id' => $this->status->id]);
    }

    public function testStatusCreationRequiresName()
    {
        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), ['name' => '']);

        $response->assertSessionHasErrors(['name']);
    }

    public function testStatusNameMustBeUnique()
    {
        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), $this->duplicateStatusData);

        $response->assertSessionHasErrors(['name']);
    }

    public function testStatusNameHasMax255Chars()
    {
        $response = $this->actingAs($this->user)
            ->post(route('task_statuses.store'), $this->longNameData);

        $response->assertSessionHasErrors(['name']);
    }
}
