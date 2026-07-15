<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(private ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function createFolder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'owner_id' => 'required|uuid',
            'parent_id' => 'nullable|uuid',
            'name_blob' => 'required|string',
        ]);

        $folder = $this->itemService->createFolder(
            $validated['owner_id'],
            $validated['parent_id'],
            $validated['name_blob']
        );

        return response()->json($folder, 201);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all();
        return response()->json($items);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
    }
}
