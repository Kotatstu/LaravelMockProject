<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct(private WarehouseRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->repository->all());
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

        $warehouse = $this->repository->create($validated);

        return response()->json($warehouse, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        return $this->repository->find($warehouse->id);
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

        $warehouse = $this->repository->update($warehouse, $validated);

        return response()->json($warehouse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->stocks()->exists())
        {
            return response()->json([
                'message' => 'Cannot delete this category cuz it still has products asigned to it'
            ], 
            409);
        }

        $this->repository->delete($warehouse);

        return response()->json(null, 204);
    }
}
