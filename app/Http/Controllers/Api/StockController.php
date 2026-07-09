<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stock = Stock::with(['warehouse', 'product'])->get();

        return response()->json($stock);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $exist = Stock::where('warehouse_id', $validated['warehouse_id'])
        ->where('product_id', $validated['product_id'])
        ->exists();

        if ($exist)
        {
            return response()->json([
                'message' => 'A stock record already exists'
            ], 409
            );
        }

        $stock = Stock::create($validated);

        return response()->json($stock->load(['warehouse', 'product']), 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        return response()->json($stock->load(['warehouse', 'product']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:0']
        ]);

        $stock->update($validated);

        return response()->json($stock->load(['warehouse', 'product']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return response()->json(null, 204);
    }
}
