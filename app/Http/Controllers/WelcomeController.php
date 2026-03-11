<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class WelcomeController extends Controller
{
    /**
     * Display a listing of the available rooms for the marketplace view.
     */
    public function index(Request $request)
    {
        $query = Room::query()->where('is_active', true);

        // Optional filtering by building
        if ($request->filled('building')) {
            $query->where('building', 'like', '%' . $request->building . '%');
        }

        // Optional filtering by scope (universitas/fakultas)
        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        // Get unique buildings for the filter wrapper
        $buildings = Room::where('is_active', true)->whereNotNull('building')->distinct()->pluck('building');

        $rooms = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('welcome', compact('rooms', 'buildings'));
    }
}
