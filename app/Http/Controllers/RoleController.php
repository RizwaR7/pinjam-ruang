<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $roles = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'slug'        => 'nullable|string|max:100|unique:roles,slug',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah terdaftar.',
            'slug.unique'   => 'Slug sudah digunakan, gunakan slug lain.',
        ]);

        $validated['slug'] = $validated['slug']
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Pastikan slug unik meski di-generate otomatis
        $originalSlug = $validated['slug'];
        $slugSuffix = 1;
        while (Role::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $slugSuffix++;
        }

        Role::create($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function show(Role $role)
    {
        $role->loadCount('users');
        $role->load('users');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'slug'        => ['nullable', 'string', 'max:100', Rule::unique('roles', 'slug')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah terdaftar.',
            'slug.unique'   => 'Slug sudah digunakan, gunakan slug lain.',
        ]);

        $validated['slug'] = $validated['slug']
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Pastikan slug unik jika berubah
        if ($validated['slug'] !== $role->slug) {
            $originalSlug = $validated['slug'];
            $slugSuffix = 1;
            while (Role::where('slug', $validated['slug'])->where('id', '!=', $role->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $slugSuffix++;
            }
        }

        $role->update($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role "' . $role->name . '" berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        // Cegah hapus role yang masih memiliki user
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role "' . $role->name . '" tidak bisa dihapus karena masih memiliki ' . $role->users()->count() . ' pengguna.');
        }

        $roleName = $role->name;
        $role->menus()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role "' . $roleName . '" berhasil dihapus.');
    }
}
