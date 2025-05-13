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
        public function authenticated_user_can_access_create_form()
        {
                $response = $this->actingAs($this->user)
                ->get(route('tasks.create'));

                $response->assertStatus(200)
                ->assertViewIs('tasks.create');
        }

        #[Test]
        public function guest_cannot_access_create_form()
        {
                $response = $this->get(route('tasks.create'));

                $response->assertRedirect(route('login'));
        }

        #[Test]
        public function authenticated_user_can_create_task()
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
        public function task_creation_requires_name()
        {
                $response = $this->actingAs($this->user)
                ->post(route('tasks.store'), ['name' => '']);

                $response->assertSessionHasErrors(['name']);
        }

        #[Test]
        public function task_name_must_be_unique()
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
        public function task_name_has_max_255_chars()
        {
                $response = $this->actingAs($this->user)
                ->post(route('tasks.store'), [
                        'name' => str_repeat('a', 256)
                ]);

                $response->assertSessionHasErrors(['name']);
        }

        #[Test]
        public function guest_cannot_create_task()
        {
                $response = $this->post(route('tasks.store'), [
                'name' => 'Гостевая задача'
                ]);

                $response->assertRedirect(route('login'));
        }
}