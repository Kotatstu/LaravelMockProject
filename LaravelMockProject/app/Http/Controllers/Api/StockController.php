<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Repositories\StockRepository;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->repository->all(['warehouse', 'product']));
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

        $stock = $this->repository->create($validated, ['warehouse', 'product']);

        return response()->json($stock, 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        return response()->json($this->repository->find($stock->id, ['warehouse', 'product']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:0']
        ]);

        $stock = $this->repository->update($stock, $validated, ['warehouse', 'product']);

        return response()->json($stock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $this->repository->delete($stock);

        return response()->json(null, 204);
    }
}
