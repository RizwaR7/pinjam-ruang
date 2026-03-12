<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::whereNull('parent_id')
            ->where('context', 'admin')
            ->orderBy('sort_order')
            ->get();

        $roles = Role::all();

        return view('admin.menus.index', compact('menus', 'roles'));
    }

    /**Search */
    public function search(Request $request)
    {
        $searchTerm = $request->query('q');

        // Kalau kosong, render ulang seperti default
        if (!$searchTerm) {
            $dynamicMenus = Menu::whereNull('parent_id')
                ->where('context', 'admin')
                ->with('children') // penting!
                ->orderBy('sort_order')
                ->get();

            return view('layouts.partials.admin.menu-default', compact('dynamicMenus'));
        }

        $menus = Menu::where('context', 'admin')
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy('sort_order')
            ->get();

        return view('layouts.partials.admin.menu-search-result', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menus = Menu::whereNull('parent_id')
            ->where('context', 'admin')
            ->orderBy('sort_order')
            ->get();

        $roles = Role::all();

        return view('admin.menus.create', compact('menus', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'route_name' => 'nullable|string',
            'icon' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'roles' => 'required|array'
        ]);

        $menu = Menu::create([
            'context' => 'admin',
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'route_name' => $request->route_name,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        $menu->roles()->sync($request->roles);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $menus = Menu::whereNull('parent_id')
            ->where('context', 'admin')
            ->where('id', '!=', $menu->id)
            ->orderBy('sort_order')
            ->get();

        $roles = Role::all();

        return view('admin.menus.edit', compact('menu', 'menus', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'route_name' => 'nullable|string',
            'icon' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'roles' => 'required|array'
        ]);

        $menu->update([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'route_name' => $request->route_name,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        $menu->roles()->sync($request->roles);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        if ($menu->children()->exists()) {
            return redirect()
                ->route('admin.menus.index')
                ->with('error', 'Cannot delete parent menu with children.');
        }

        $menu->roles()->detach();
        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }

    /**
     * Reorder menus via drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:menus,id',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        \DB::transaction(function () use ($request) {
            foreach ($request->order as $item) {
                Menu::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
        });

        return response()->json(['success' => true]);
    }
}
