<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::all();

        return $warehouses;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $warehouse = Warehouse::create($validated);

        return response()->json($warehouse, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        return $warehouse;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
        ]);

        $warehouse->update($validated);

        return response()->json($warehouse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return response()->json(null, 204);
    }
}
