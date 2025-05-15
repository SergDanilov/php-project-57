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
    public function authenticated_user_can_access_create_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('labels.create'));

        $response->assertStatus(200)
            ->assertViewIs('labels.create');
    }

    #[Test]
    public function guest_cannot_access_create_form()
    {
        $response = $this->get(route('labels.create'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_create_label()
    {
        $labelData = ['name' => 'Новая метка'];

        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), $labelData);

        $response->assertRedirect(route('labels.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('labels', $labelData);
    }

    #[Test]
    public function label_creation_requires_name()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), ['name' => '']);

        $response->assertSessionHasErrors(['name']);
    }

        #[Test]
        public function label_name_must_be_unique()
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
    public function label_name_has_max_64_chars()
    {
        $response = $this->actingAs($this->user)
            ->post(route('labels.store'), [
                'name' => str_repeat('a', 65)
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function guest_cannot_create_label()
    {
        $response = $this->post(route('labels.store'), [
            'name' => 'Гостевой статус'
        ]);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function label_can_be_attached_to_task()
    {
        $label = Label::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store', [$this->task->id, $label->id]));

        $response->assertRedirect();
        $this->assertDatabaseHas('label', [
            'task_id' => $this->task->id,
            'label_id' => $label->id
        ]);
    }


    #[Test]
        public function cannot_delete_label_attached_to_task()
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
        $response->assertStatus(403); // Или другой подходящий код ошибки
        $this->assertDatabaseHas('labels', ['id' => $label->id]);
        $this->assertDatabaseHas('task_label', [
                'task_id' => $task->id,
                'label_id' => $label->id
        ]);
        }
}
