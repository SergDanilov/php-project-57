<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Status;
use App\Models\Label;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = QueryBuilder::for(Task::class)
            ->with(['status', 'creator', 'assignee', 'labels'])
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

        $statuses = Status::pluck('name', 'id');
        $users = User::pluck('name', 'id');
        $labels = Label::pluck('name', 'id');

        return view('tasks.index', compact('tasks', 'statuses', 'users', 'labels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Task::class);

        $statuses = Status::all();
        $users = User::all();
        $labels = Label::all();
        $task = new Task();

        return view('tasks.create', compact('statuses', 'users', 'labels', 'task'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->authorize('create', Task::class);

        $request->validate([
                'name' => 'required|unique:tasks|max:255',
                'description' => 'max:1024',
                'status_id' => 'required|exists:statuses,id',
                'assigned_to_id' => 'exists:users,id',
            ], [
                'name.required' => 'Это обязательное поле',
                'name.unique' => 'Задача с таким именем уже существует',
            ]
        );

        $task = Task::create($request->all());

        // Привязка меток к задаче
        if ($request->has('labels')) {
            $task->labels()->attach($request->input('labels'));
        }

        return redirect()->route('tasks.index')
            ->with('success', __('messages.task__created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $statuses = Status::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.show', compact('task', 'statuses', 'users', 'labels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $statuses = Status::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.edit', compact('task', 'statuses', 'users', 'labels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $request->validate([
            'name' => 'required|unique:tasks,name,' . $task->id . '|max:255',
            'labels' => 'array',
            'labels.*' => 'exists:labels,id',
        ]);

        $task->update($request->all());
         // Обновление меток
        if ($request->has('labels')) {
            $task->labels()->sync($request->input('labels'));
        } else {
            $task->labels()->sync([]); // Удалить все метки, если метки не переданы
        }

        return redirect()->route('tasks.index')
            ->with('success', __('messages.task__updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
        ->with('success', __('messages.task_deleted'));
    }
}
