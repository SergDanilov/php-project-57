<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StatusController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::select('id', 'name', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    // ->get();
        return view('statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Status::class);
        return view('statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Status::class);
        $request->validate([
            'name' => 'required|unique:statuses|max:255',
            ], [
                'name.required' => 'Это обязательное поле',
                'name.unique' => 'Статус с таким именем уже существует',
            ]);

        Status::create($request->all());

        return redirect()->route('task_statuses.index')
            ->with('success', __('messages.status__created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $task_status)
    {
        $this->authorize('update', $task_status);
        return view('statuses.edit', compact('task_status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $task_status)
    {
        $this->authorize('update', $task_status);
        $request->validate([
            'name' => 'required|unique:statuses,name,' . $task_status->id . '|max:255',
        ]);

        $task_status->update($request->all());

        return redirect()->route('task_statuses.index')
            ->with('success', __('messages.status__updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $task_status)
    {
        $this->authorize('delete', $task_status);

        if ($task_status->tasks()->exists()) {
            return back()->with('error', __('messages.status__cannot__be__deleted'));
        }

        $task_status->delete();
        return redirect()->route('task_statuses.index')->with('success', __('messages.status__deleted'));
    }
}
