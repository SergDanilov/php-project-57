<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StatusController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $statuses = TaskStatus::select('id', 'name', 'created_at')
                    ->latest('updated_at')
                    ->paginate(10);
        return view('statuses.index', compact('statuses'));
    }

    public function create()
    {
        $this->authorize('create', TaskStatus::class);
        return view('statuses.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', TaskStatus::class);
        $validatedData = $request->validate([
            'name' => 'required|unique:task_statuses|max:255',
            ], [
                'name.required' => __('messages.required__field'),
                'name.unique' => __('messages.status__name__already__exists'),
            ]);

        TaskStatus::create($validatedData);

        return to_route('task_statuses.index')
            ->with('success', __('messages.status__created'));
    }

    public function edit(TaskStatus $task_status)
    {
        $this->authorize('update', $task_status);
        return view('statuses.edit', compact('task_status'));
    }

    public function update(Request $request, TaskStatus $task_status)
    {
        $this->authorize('update', $task_status);
        $validatedData = $request->validate([
            'name' => 'required|unique:task_statuses,name,' . $task_status->id . '|max:255',
        ]);

        $task_status->update($validatedData);

        return to_route('task_statuses.index')
            ->with('success', __('messages.status__updated'));
    }

    public function destroy(TaskStatus $task_status)
    {
        $this->authorize('delete', $task_status);

        if ($task_status->tasks()->exists()) {
            return back()->with('error', __('messages.status__cannot__be__deleted'));
        }

        $task_status->delete();
        return to_route('task_statuses.index')->with('success', __('messages.status__deleted'));
    }
}
