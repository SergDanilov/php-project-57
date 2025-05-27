<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LabelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create();
    }

    #[Test]
    public function authenticatedUserCanAccessCreateForm()
    {
        $response = $this->actingAs($this->user)
            ->get(route('labels.create'));

        $response->assertStatus(200)
            ->assertViewIs('labels.create');
    }

    #[Test]
    public function guestCannotAccessCreateForm()
    {
        $response = $this->get(route('labels.create'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticatedUserCanCreateLabel()
    {
        $labelData = ['name' => 'Новая метка'];

        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), $labelData);

        $response->assertRedirect(route('labels.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('labels', $labelData);
    }

    #[Test]
    public function labelCreationRequiresName()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), ['name' => '']);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function labelNameMustBeUnique()
    {
        DB::beginTransaction();

        try {
            Label::factory()->create(['name' => 'Дубль']);

            $response = $this->actingAs($this->user)
                        ->post(route('labels.store'), ['name' => 'Дубль']);

            $response->assertSessionHasErrors(['name']);
            } finally {
                DB::rollBack();
            }
    }

    #[Test]
    public function labelNameHasMax64Chars()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), [
                'name' => str_repeat('a', 65)
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function guestCannotCreateLabel()
    {
        $response = $this->post(route('labels.store'), [
            'name' => 'Гостевой статус'
        ]);

        $response->assertRedirect(route('login'));
    }


    #[Test]
    public function cannotDeleteLabelAttachedToTask()
    {
        // Создаем метку и задачу
        $label = Label::factory()->create();
        $task = Task::factory()->create(['created_by_id' => $this->user->id]);

        // Прикрепляем метку к задаче
        $task->labels()->attach($label->id);

        // Пытаемся удалить метку
        $response = $this->actingAs($this->user)
                ->delete(route('labels.destroy', $label->id));

        // Проверяем, что удаление не произошло
        $response->assertStatus(403);
        $response->assertSessionHas('error', __('messages.label__cannot__be__deleted'));

        $this->assertDatabaseHas('labels', ['id' => $label->id]);
        $this->assertDatabaseHas('task_label', [
                'task_id' => $task->id,
                'label_id' => $label->id
        ]);
    }
}
