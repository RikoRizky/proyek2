<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function index(): View
    {
        $modules = Module::query()->withCount('requirements')->orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('admin.modules.index', compact('modules'));
    }

    public function create(): View
    {
        return view('admin.modules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Module::query()->create($validated);

        return redirect()->route('admin.modules.index')->with('status', 'Modul berhasil ditambahkan.');
    }

    public function show(Module $module): View
    {
        $module->load(['requirements' => fn ($q) => $q->orderBy('sort_order')]);

        return view('admin.modules.show', compact('module'));
    }

    public function edit(Module $module): View
    {
        return view('admin.modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $module->update($validated);

        // Setelah update berhasil, diarahkan ke halaman index
        return redirect()->route('admin.modules.index')->with('status', 'Modul diperbarui.');
    }

    public function destroy(Module $module): RedirectResponse
    {
        $module->delete();

        return redirect()->route('admin.modules.index')->with('status', 'Modul dihapus.');
    }
}