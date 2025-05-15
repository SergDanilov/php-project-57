<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Status;
use App\Models\Label;
use App\Models\User;
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
        $tasks = Task::with(['status', 'creator', 'assignee', 'labels'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = Status::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.create', compact('statuses', 'users', 'labels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tasks|max:255',
            'description' => 'max:1024',
            'status_id' => 'required|exists:statuses,id',
            'assigned_to_id' => 'exists:users,id',
        ]);

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

        return view('tasks.show', compact('task','statuses', 'users', 'labels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $statuses = Status::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.edit', compact('task','statuses', 'users', 'labels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|unique:tasks,name,'.$task->id.'|max:255',
            'labels' => 'array', // Добавьте это правило валидации
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
