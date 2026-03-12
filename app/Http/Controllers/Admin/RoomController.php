<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::withCount('bookings');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%')
                    ->orWhere('building', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        if ($request->filled('building')) {
            $query->where('building', $request->building);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rooms = $query->orderBy('building')->orderBy('name')->paginate(10)->withQueryString();

        $buildings = Room::whereNotNull('building')->distinct()->pluck('building')->sort()->values();
        $faculties = Room::whereNotNull('faculty')->distinct()->pluck('faculty')->sort()->values();

        $rawStats = Room::selectRaw('
            COUNT(*) as total,
            SUM(status = "tersedia") as tersedia,
            SUM(status = "dipakai") as dipakai,
            SUM(status = "maintenance") as maintenance
        ')->first();

        $stats = [
            'total'       => (int) ($rawStats->total ?? 0),
            'tersedia'    => (int) ($rawStats->tersedia ?? 0),
            'dipakai'     => (int) ($rawStats->dipakai ?? 0),
            'maintenance' => (int) ($rawStats->maintenance ?? 0),
        ];

        return view('admin.rooms.index', compact('rooms', 'buildings', 'faculties', 'stats'));
    }

    public function create()
    {
        $buildings = Room::whereNotNull('building')->distinct()->pluck('building')->sort()->values();
        $faculties = Room::whereNotNull('faculty')->distinct()->pluck('faculty')->sort()->values();

        return view('admin.rooms.create', compact('buildings', 'faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:rooms,code',
            'scope' => 'required|in:universitas,fakultas',
            'faculty' => 'nullable|required_if:scope,fakultas|string|max:255',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:tersedia,dipakai,maintenance',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['scope'] === 'universitas') {
            $validated['faculty'] = null;
        }

        Room::create($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Ruangan "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function show(Room $room)
    {
        $room->loadCount('bookings');
        $upcomingBookings = $room->bookings()
            ->with('user')
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        return view('admin.rooms.show', compact('room', 'upcomingBookings'));
    }

    public function edit(Room $room)
    {
        $buildings = Room::whereNotNull('building')->distinct()->pluck('building')->sort()->values();
        $faculties = Room::whereNotNull('faculty')->distinct()->pluck('faculty')->sort()->values();

        return view('admin.rooms.edit', compact('room', 'buildings', 'faculties'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('rooms', 'code')->ignore($room->id)],
            'scope' => 'required|in:universitas,fakultas',
            'faculty' => 'nullable|required_if:scope,fakultas|string|max:255',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:tersedia,dipakai,maintenance',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['scope'] === 'universitas') {
            $validated['faculty'] = null;
        }

        $room->update($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Ruangan "' . $room->name . '" berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->bookings()->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Ruangan "' . $room->name . '" tidak bisa dihapus karena masih memiliki peminjaman aktif.');
        }

        $roomName = $room->name;
        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Ruangan "' . $roomName . '" berhasil dihapus.');
    }
}
