<?php

namespace App\Http\Controllers;

use App\Models\Shelf;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    public function index()
    {
        $shelves = Shelf::with(['layers.books'])->orderBy('sort_order', 'asc')->get();
        // 加總每層書的數量
        $shelves = $shelves->map(function ($shelf) {
            $totalBooks = $shelf->layers->sum(function ($layer) {
                return $layer->books->count();
            });
            $totalCapacity = $shelf->layers->sum('capacity');
            $layerCount = $shelf->layers->count();
            return [
                'id' => $shelf->id,
                'name' => $shelf->name,
                'location' => $shelf->location,
                'layer_count' => $layerCount,
                'capacity' => $totalCapacity,
                'total_books' => $totalBooks,
            ];
        });
        return response()->json($shelves);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string',
            'layer_count' => 'required|integer|min:1',
            'capacity_per_layer' => 'required|integer|min:1',
        ]);

         // 取得目前最大 sort_order 值，預設為 0
        $maxOrder = Shelf::max('sort_order') ?? 0;
        $shelf = Shelf::create([
            'name' => $request->name,
            'location' => $request->location,
            'sort_order' => $maxOrder + 1,
        ]);

        // 自動建立指定層數，每層設 capacity
        for ($i = 1; $i <= $request->layer_count; $i++) {
            $shelf->layers()->create([
                'level' => $i,
                'capacity' => $request->capacity_per_layer,
            ]);
        }

        return response()->json($shelf->load('layers'), 201);
    }


    public function addLayer(Request $request, Shelf $shelf)
    {
        $layer = $shelf->layers()->create([
            'level' => $request->input('level', $shelf->layers()->count() + 1)
        ]);
        return response()->json($layer);
    }

    public function reorder(Request $request)
    {
        $shelves = $request->input('shelves');
    
        foreach ($shelves as $item) {
            Shelf::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
    
        return response()->json(['message' => 'ok']);
    }
    

    public function show($id)
    {
        $shelf = Shelf::with(['layers.books'])->find($id);

        if ($shelf) {
            return response()->json($shelf);
        } else {
            return response()->json(['message' => 'Shelf not found'], 404);
        }
    }
}
