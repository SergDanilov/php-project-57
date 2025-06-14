<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LabelController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $labels = Label::latest('updated_at')
                  ->paginate(10);
        return view('labels.index', compact('labels'));
    }

    public function create()
    {
        $this->authorize('create', Label::class);
        return view('labels.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Label::class);
        $validatedData = $request->validate([
            'name' => 'required|unique:labels|max:64',
            'description' => 'nullable|max:528',
            ], [
                'name.required' => __('messages.required__field'),
                'name.unique' => __('messages.label__name__already__exists'),
                'description.max' => __('messages.much__words'),
            ]);

        Label::create($validatedData);

        return to_route('labels.index')
            ->with('success', __('messages.label__created'));
    }

    public function edit(Label $label)
    {
        $this->authorize('update', $label);
        return view('labels.edit', compact('label'));
    }

    public function update(Request $request, Label $label)
    {
        $this->authorize('update', $label);
        $validatedData = $request->validate([
            'name' => 'required|unique:task_statuses,name,' . $label->id . '|max:64',
            'description' => 'nullable|max:528',
        ], [
            'name.required' => __('messages.required__field'),
            'description.max' => __('messages.much__words'),
        ]);

        $label->update($validatedData);

        return to_route('labels.index')
            ->with('success', __('messages.label__updated'));
    }

    public function destroy(Label $label)
    {
        $this->authorize('delete', $label);

        if ($label->tasks()->exists()) {
            return to_route('labels.index')
            ->with('error', __('messages.label__cannot__be__deleted'))
            ->setStatusCode(403);
        }


        $label->delete();
        return to_route('labels.index')
            ->with('success', __('messages.label__deleted'));
    }
}
