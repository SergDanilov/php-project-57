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
        $request->validate([
            'name' => 'required|unique:labels|max:64',
            ], [
                'name.required' => 'Это обязательное поле',
                'name.unique' => 'Метка с таким именем уже существует',
            ]);

        Label::create($request->all());

        return to_route('labels.index')
            ->with('success', __('messages.label__created'));
    }

    public function show(Label $label)
    {
        //
    }

    public function edit(Label $label)
    {
        $this->authorize('update', $label);
        return view('labels.edit', compact('label'));
    }

    public function update(Request $request, Label $label)
    {
        $this->authorize('update', $label);
        $request->validate([
            'name' => 'required|unique:task_statuses,name,' . $label->id . '|max:64',
        ]);

        $label->update($request->all());

        return to_route('labels.index')
            ->with('success', __('messages.label__updated'));
    }

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
        return to_route('labels.index')
            ->with('success', __('messages.label__deleted'));
    }
}
