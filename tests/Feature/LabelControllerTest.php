<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LabelControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Task $task;
    protected Label $label;
    protected array $labelData;
    protected array $duplicateLabelData;
    protected array $longLabelData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create(['created_by_id' => $this->user->id]);
        $this->label = Label::factory()->create(['name' => 'Существующая метка']);

        // Основные тестовые данные
        $this->labelData = [
            'name' => 'Тестовая метка'
        ];

        // Данные для проверки дублирования
        $this->duplicateLabelData = [
            'name' => 'Дубль метки'
        ];

        // Данные для проверки длины
        $this->longLabelData = [
            'name' => str_repeat('a', 65)
        ];

        // Прикрепляем метку к задаче для теста удаления
        $this->task->labels()->attach($this->label->id);
    }

    public function testAuthenticatedUserCanAccessCreateForm()
    {
        $response = $this->actingAs($this->user)
            ->get(route('labels.create'));

        $response->assertOk()
            ->assertViewIs('labels.create');
    }

    public function testGuestCannotAccessCreateForm()
    {
        $response = $this->get(route('labels.create'));
        $response->assertRedirect(route('login'));
    }

    public function testAuthenticatedUserCanCreateLabel()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), $this->labelData);

        $response->assertRedirect(route('labels.index'))
            ->assertSessionDoesntHaveErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('labels', $this->labelData);
    }

    public function testLabelCreationRequiresName()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), ['name' => '']);

        $response->assertSessionHasErrors(['name']);
    }

    public function testLabelNameMustBeUnique()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), ['name' => $this->label->name]);

        $response->assertSessionHasErrors(['name']);
    }

    public function testLabelNameHasMax64Chars()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), $this->longLabelData);

        $response->assertSessionHasErrors(['name']);
    }

    public function testGuestCannotCreateLabel()
    {
        $response = $this->post(route('labels.store'), $this->labelData);
        $response->assertRedirect(route('login'));
    }

    public function testCannotDeleteLabelAttachedToTask()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('labels.destroy', $this->label->id));

        $response->assertStatus(403)
            ->assertSessionHas('error', __('messages.label__cannot__be__deleted'));

        $this->assertDatabaseHas('labels', ['id' => $this->label->id]);
        $this->assertDatabaseHas('task_label', [
            'task_id' => $this->task->id,
            'label_id' => $this->label->id
        ]);
    }

    public function testCanDeleteUnattachedLabel()
    {
        $unattachedLabel = Label::factory()->create();
        $response = $this->actingAs($this->user)
            ->delete(route('labels.destroy', $unattachedLabel->id));

        $response->assertRedirect(route('labels.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('labels', ['id' => $unattachedLabel->id]);
    }
}
