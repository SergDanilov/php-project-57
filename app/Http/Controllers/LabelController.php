<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LabelController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labels = Label::orderBy('created_at', 'desc')
                  ->paginate(10);
        return view('labels.index', compact('labels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Label::class);
        return view('labels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Label::class);
        $request->validate([
            'name' => 'required|unique:labels|max:64',
            ], [
                'name.required' => 'Это обязательное поле',
                'name.unique' => 'Метка с таким именем уже существует',
            ]);

        Label::create($request->all());

        return redirect()->route('labels.index')
            ->with('success', __('messages.label__created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Label $label)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Label $label)
    {
        $this->authorize('update', $label);
        return view('labels.edit', compact('label'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Label $label)
    {
        $this->authorize('update', $label);
        $request->validate([
            'name' => 'required|unique:statuses,name,' . $label->id . '|max:64',
        ]);

        $label->update($request->all());

        return redirect()->route('labels.index')
            ->with('success', __('messages.label__updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Label $label)
    {
        $this->authorize('delete', $label);

        if ($label->tasks()->exists()) {
        return redirect()
            ->route('labels.index')
            ->with('error', __('messages.label__cannot__be__deleted'))
            ->setStatusCode(403);
        }


        $label->delete();
        return redirect()->route('labels.index')
            ->with('success', __('messages.label__deleted'));
    }
}
