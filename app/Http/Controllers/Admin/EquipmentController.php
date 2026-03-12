<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $equipment = $query->orderBy('category')->orderBy('name')->paginate(15)->appends(request()->query());

        $rawStats = Equipment::selectRaw('
            COUNT(*) as total,
            SUM(is_available = 1) as available,
            SUM(condition = "baik") as baik,
            SUM(condition IN ("rusak_ringan", "rusak_berat")) as rusak
        ')->first();

        $stats = [
            'total'     => (int) ($rawStats->total ?? 0),
            'available' => (int) ($rawStats->available ?? 0),
            'baik'      => (int) ($rawStats->baik ?? 0),
            'rusak'     => (int) ($rawStats->rusak ?? 0),
        ];

        return view('admin.equipment.index', compact('equipment', 'stats'));
    }

    public function create()
    {
        return view('admin.equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:equipment,code',
            'description' => 'nullable|string',
            'category' => 'required|in:elektronik,furniture,audio_visual,lainnya',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['is_available'] = $request->has('is_available');
        Equipment::create($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load('bookings');
        return view('admin.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        return view('admin.equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:equipment,code,' . $equipment->id,
            'description' => 'nullable|string',
            'category' => 'required|in:elektronik,furniture,audio_visual,lainnya',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $equipment->update($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('admin.equipment.index')
            ->with('success', 'Fasilitas berhasil dihapus.');
    }
}
