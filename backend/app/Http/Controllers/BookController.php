<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Layer;
use App\Models\Shelf;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q');
        $books = Book::with(['layer.shelf'])
            ->where('title', 'like', "%$q%")
            ->orWhere('isbn', 'like', "%$q%")
            ->get()
            ->map(fn($book) => [
                'title' => $book->title,
                'isbn' => $book->isbn,
                'shelf' => $book->layer->shelf->name,
                'layer' => $book->layer->level,
                'position' => $book->position,
            ]);

        return response()->json($books);
    }

    public function storeAutoGlobal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
        ]);

        $layers = Layer::with('shelf')
                    ->withCount('books')
                    ->get()
                    ->filter(fn ($layer) => $layer->books_count < $layer->capacity)
                    ->sortBy(fn ($layer) => [
                        $layer->shelf->sort_order,
                        $layer->level,
                        $layer->books_count,
                    ]);

        $targetLayer = $layers->first();

        if (!$targetLayer) {
            return response()->json([
                'error' => '所有書櫃皆已滿，請新增樓層後再試。'
            ], 422);
        }
        $position = $targetLayer->books_count + 1;

        $book = $targetLayer->books()->create([
            'title' => $validated['title'],
            'isbn' => $validated['isbn'],
            'position' => $position,
        ]);
    
        return response()->json([
            'shelf' => $targetLayer->shelf->name,
            'layer' => $targetLayer->level,
            'position' => $position,
        ]);
    }

    public function batchSearch(Request $request)
    {
        $validated = $request->validate([
            'keywords' => 'required|array|max:3',
            'keywords.*' => 'string'
        ]);

        $query = $request->input('keywords');

        $books = Book::with(['layer.shelf'])
            ->where(function ($q) use ($query) {
                foreach ($query as $keyword) {
                    $q->orWhere('title', 'like', '%' . $keyword . '%')
                    ->orWhere('isbn', 'like', '%' . $keyword . '%');
                }
            })
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'isbn' => $book->isbn,
                    'shelf_sort_order' => $book->layer->shelf->sort_order ?? '-',
                    'shelf_name' => $book->layer->shelf->name ?? '-',
                    'layer_level' => $book->layer->level ?? '-',
                    'position' => $book->position ?? '-',
                    '_sort_shelf' => $book->layer->shelf->sort_order ?? 0,
                    '_sort_layer' => $book->layer->level ?? 0,
                    '_sort_slot' => $book->position ?? 0,
                ];
            });

        $sorted = $books->sortBy([
            ['_sort_shelf', 'asc'],
            ['_sort_layer', 'asc'],
            ['_sort_slot', 'asc'],
        ])->values();

        $results = $sorted->map(function ($book) {
            unset($book['_sort_shelf'], $book['_sort_layer'], $book['_sort_slot']);
            return $book;
        });

        return response()->json([
            'results' => $results,
            'count' => $results->count(),
        ]);
    }


    public function storeToShelfAuto(Request $request, Shelf $shelf)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
        ]);

        $layers = $shelf->layers()->withCount('books')->orderBy('level')->get();

        foreach ($layers as $layer) {
            if ($layer->books_count < $layer->capacity) {
                $position = $layer->books_count + 1;

                $book = $layer->books()->create(array_merge($validated, [
                    'position' => $position,
                ]));

                return response()->json(['message' => 'ok']);
            }
        }

        return response()->json([
            'error' => '所有樓層皆已滿，請新增樓層後再試。',
        ], 422);
    }

    public function storeToSpecificLayer(Request $request, Shelf $shelf, Layer $layer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
        ]);
        $maxPosition = $layer->books()->max('position') ?? 0;
        $position = $maxPosition + 1;
        $book = $layer->books()->create(array_merge($validated, ['position' => $position]));

        return response()->json([
            'message' => '書本已新增至指定樓層',
            'book' => $book,
        ]);
    }
}
