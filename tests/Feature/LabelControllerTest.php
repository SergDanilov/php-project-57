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

    // Проверка индексной страницы и пагинации
    public function testIndexDisplaysLabels()
    {
        // Создаем 15 статусов, чтобы проверить пагинацию
        Label::factory()->count(15)->create();

        $response = $this->actingAs($this->user)
            ->get(route('labels.index'));

        $response->assertOk()
            ->assertViewIs('labels.index')
            ->assertViewHas('labels') // Проверяем, что переданы все метки
            ->assertSeeText(Label::latest('updated_at')->first()->name); // Проверяем порядок

        // Проверяем пагинацию (должно быть 10 записей на странице)
        $this->assertCount(10, $response->viewData('labels'));
    }

    public function testGuestCanViewLabelsIndex()
    {
        $response = $this->get(route('labels.index'));

        $response->assertOk()
            ->assertViewIs('labels.index');
    }

    // Доступ к форме создания
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

    // Создание меток
    public function testAuthenticatedUserCanCreateLabel()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), $this->labelData);

        $response->assertRedirect(route('labels.index'))
            ->assertSessionDoesntHaveErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('labels', $this->labelData);
    }

    public function testGuestCannotCreateLabel()
    {
        $response = $this->post(route('labels.store'), $this->labelData);
        $response->assertRedirect(route('login'));
    }

    // Править
    public function testAuthenticatedUserCanAccessEditForm()
    {
        $response = $this->actingAs($this->user)
            ->get(route('labels.edit', $this->label));

        $response->assertOk()
            ->assertViewIs('labels.edit')
            ->assertViewHas('label', $this->label);
    }

    public function testGuestCannotAccessEditForm()
    {
        $response = $this->get(route('labels.edit', $this->label));
        $response->assertRedirect(route('login'));
    }

    // Редактирование меток
    public function testAuthenticatedUserCanUpdateLabel()
    {
        $updatedData = ['name' => 'Updated Label'];

        $response = $this->actingAs($this->user)
            ->put(route('labels.update', $this->label), $updatedData);

        $response->assertRedirect(route('labels.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('labels', $updatedData);
    }

    public function testGuestCannotUpdateLabel()
    {
        $response = $this->put(route('labels.update', $this->label), ['name' => 'Updated']);
        $response->assertRedirect(route('login'));
    }

    // Удаление меток
    public function testAuthenticatedUserCanDeleteUnusedLabel()
    {
        // 1. Создаем тестовую метку без привязки к задачам
        $label = Label::factory()->create();

        // 2. Пытаемся удалить авторизованным пользователем
        $response = $this->actingAs($this->user)
            ->delete(route('labels.destroy', $label));

        // 3. Проверяем редирект и сообщение об успехе
        $response->assertRedirect(route('labels.index'))
            ->assertSessionHas('success');

        // 4. Проверяем, что метки больше нет в БД
        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }

    public function testCannotDeleteLabelAttachedToTask()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('labels.destroy', $this->label->id));

        $response->assertForbidden()
            ->assertSessionHas('error', __('messages.label__cannot__be__deleted'));

        $this->assertDatabaseHas('labels', ['id' => $this->label->id]);
        $this->assertDatabaseHas('task_label', [
            'task_id' => $this->task->id,
            'label_id' => $this->label->id
        ]);
    }

    public function testGuestCannotDeleteLabel()
    {
        $response = $this->delete(route('labels.destroy', $this->label));
        $response->assertRedirect(route('login'));
    }

    // остальное
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
}
