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
        $statuses = Status::all();
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
        $request->validate([
            'name' => 'required|unique:statuses|max:255',
        ]);

        Status::create($request->all());

        return redirect()->route('task_statuses.index')
            ->with('success', 'Status created successfully.');
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
        return view('statuses.edit', compact('task_status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $task_status)
    {
        $request->validate([
            'name' => 'required|unique:statuses,name,'.$task_status->id.'|max:255',
        ]);

        $task_status->update($request->all());

        return redirect()->route('task_statuses.index')
            ->with('success', 'Status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $task_status)
    {
        $task_status->delete();

        return redirect()->route('task_statuses.index')
            ->with('success', 'Status deleted successfully');
    }
}
