<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DispatchStockDTO;
use App\Http\Controllers\Controller;
use App\Models\StockExport;
use App\Repositories\StockExportRepository;
use App\Services\StockExportService;
use Illuminate\Http\Request;

class StockExportController extends Controller
{
    public function __construct(
        private StockExportRepository $repository,
        private StockExportService $stockExportService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->repository->all(['warehouse', 'stockExportItems.product']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'unique:stock_exports,reference'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'destination' => ['required', 'string'],
            //default status is pending
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1']
        ]);

        $stockExport = $this->stockExportService->createWithItems($validated);

        return response()->json($stockExport, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockExport $stockExport)
    {
        return response()->json($this->repository->find($stockExport->id, ['warehouse', 'stockExportItems']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockExport $stockExport)
    {
        $validated = $request->validate([
            'destination' => ['sometimes', 'string']
        ]);

        $stockExport = $this->repository->update($stockExport, $validated, ['warehouse', 'stockExportItems']);

        return response()->json($stockExport);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockExport $stockExport)
    {
        $this->repository->delete($stockExport);

        return response()->json(null, 204);
    }

    public function dispatch(Request $request, StockExport $stockExport)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.stock_export_item_id' => ['required', 'exists:stock_export_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try
        {
            $dto = DispatchStockDTO::fromArray($validated);
            $stockExport = $this->stockExportService->dispatch($stockExport, $dto);

            return response()->json($stockExport);
        }
        catch (\Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }
}
