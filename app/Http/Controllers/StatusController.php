<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::orderBy('id')->paginate(10);
        return view('statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:statuses|max:255']);
        
        Status::create($request->only('name'));
        return redirect()->route('task_statuses.index')->with('success', 'Статус успешно создан');
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
    public function edit(Status $status)
    {
        return view('statuses.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $status)
    {
        $request->validate([
            'name' => 'required|unique:statuses,name,' . $status->id . '|max:255'
        ]);
        
        $status->update($request->only('name'));
        return redirect()->route('task_statuses.index')->with('success', 'Статус успешно изменён');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        if ($status->tasks()->exists()) {
            return back()->with('error', 'Нельзя удалить статус, у которого есть задачи');
        }
        
        $status->delete();
        return redirect()->route('task_statuses.index')->with('success', 'Статус успешно удалён');
    }
}
