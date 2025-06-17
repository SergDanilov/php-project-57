<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\Label;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {

        $taskQuery = Task::query()
            ->with(['status', 'creator', 'assignee', 'labels']);

        $tasks = QueryBuilder::for($taskQuery)
            ->allowedFilters([
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('created_by_id', 'creator.id'),
                AllowedFilter::exact('assigned_to_id', 'assignee.id'),
                AllowedFilter::exact('labels.id'),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['created_at', 'name'])
            ->defaultSort('-created_at')
            ->paginate(10);

        $statuses = TaskStatus::pluck('name', 'id');
        $users = User::pluck('name', 'id');
        $labels = Label::pluck('name', 'id');

        return view('tasks.index', compact('tasks', 'statuses', 'users', 'labels'));
    }

    public function create()
    {
        $this->authorize('create', Task::class);

        $statuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();
        $task = new Task();

        return view('tasks.create', compact('statuses', 'users', 'labels', 'task'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validatedData = $request->validate([
            'name' => 'required|unique:tasks|max:255',
            'description' => 'nullable|max:528',
            'status_id' => 'required|exists:task_statuses,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id'
        ], [
            'name.required' => __('messages.required__field'),
            'name.unique' => __('messages.task__name__already__exists'),
            'status_id.required' => __('messages.required__field'),
            'description.max' => __('messages.much__words'),
        ]);

        // Создаем задачу с валидными данными и отношением к автору задачи
        $task = Auth::user()->createdTasks()->create($validatedData);

        // Привязка меток, если они есть
        if (!empty($validatedData['labels'])) {
            $task->labels()->attach($validatedData['labels']);
        }

        return to_route('tasks.index')
            ->with('success', __('messages.task__created'));
    }

    public function show(Task $task)
    {
        $statuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.show', compact('task', 'statuses', 'users', 'labels'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $statuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.edit', compact('task', 'statuses', 'users', 'labels'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $validatedData = $request->validate([
            'name' => 'required|unique:tasks,name,' . $task->id . '|max:255',
            'description' => 'nullable|max:528',
            'assigned_to_id' => 'nullable|exists:users,id',
            'labels' => 'array',
            'labels.*' => 'exists:labels,id',
        ], [
            'name.required' => __('messages.required__field'),
            'description.max' => __('messages.much__words'),
        ]);

        $task->update($validatedData);
        // Обновление меток
        if ($request->has('labels')) {
            $task->labels()->sync($request->input('labels'));
        } else {
            $task->labels()->sync([]); // Удалить все метки, если метки не переданы
        }

        return to_route('tasks.index')
            ->with('success', __('messages.task__updated'));
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return to_route('tasks.index')
        ->with('success', __('messages.task__deleted'));
    }
}
