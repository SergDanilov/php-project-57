<?php

namespace Tests\Feature;

use App\Models\TaskStatus;
use App\Models\User;
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

    public function testGuestCannotCreateStatus()
    {
        $response = $this->post(route('task_statuses.store'), $this->statusData);

        $response->assertRedirect(route('login'));
    }
}
