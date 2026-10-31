<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Requirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequirementController extends Controller
{
    public function index(Module $module): View
    {
        $requirements = $module->requirements()->orderBy('sort_order')->paginate(20);

        return view('admin.requirements.index', compact('module', 'requirements'));
    }

    public function create(Module $module): View
    {
        return view('admin.requirements.create', compact('module'));
    }

    public function store(Request $request, Module $module): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $module->requirements()->create($validated);

        return redirect()->route('admin.modules.show', $module)->with('status', 'Persyaratan ditambahkan.');
    }

    public function show(Module $module, Requirement $requirement): View
    {
        abort_unless($requirement->module_id === $module->id, 404);

        return view('admin.requirements.show', compact('module', 'requirement'));
    }

    public function edit(Module $module, Requirement $requirement): View
    {
        abort_unless($requirement->module_id === $module->id, 404);

        return view('admin.requirements.edit', compact('module', 'requirement'));
    }

    public function update(Request $request, Module $module, Requirement $requirement): RedirectResponse
    {
        abort_unless($requirement->module_id === $module->id, 404);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.modules.show', $module)
                ->withErrors($validator)
                ->withInput()
                ->with('edit_requirement_id', $requirement->id);
        }

        $requirement->update($validator->validated());

        return redirect()->route('admin.modules.show', $module)->with('status', 'Persyaratan diperbarui.');
    }

    public function destroy(Module $module, Requirement $requirement): RedirectResponse
    {
        abort_unless($requirement->module_id === $module->id, 404);

        $requirement->delete();

        return redirect()->route('admin.modules.show', $module)->with('status', 'Persyaratan dihapus.');
    }
}
