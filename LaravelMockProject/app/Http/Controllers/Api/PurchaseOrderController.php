<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ReceiveStockDTO;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Repositories\PurchaseOrderRepository;
use App\Services\PurchaseOrderService;
use App\Services\StockReceiveService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private StockReceiveService $StockReceiveservice,
        private PurchaseOrderService $purchaseOrderService,
        private PurchaseOrderRepository $repository
        ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return response()->json($this->repository->all(['supplier', 'items']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'unique:purchase_orders,reference'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'status' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1']
        ]);

        $purchase = $this->purchaseOrderService->createWithItems($validated);

        return response()->json($purchase, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder = $this->repository->find($purchaseOrder->id, ['supplier', 'items']);

        return response()->json($purchaseOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string']
        ]);

        $purchaseOrder = $this->repository->update($purchaseOrder, $validated, ['supplier', 'items']);

        return response()->json($purchaseOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->repository->delete($purchaseOrder);

        return response()->json(null, 204);
    }

    //
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'integer', 'exists:purchase_order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1']
        ]);
        
        try
        {
            $dto = ReceiveStockDTO::fromArray($validated);
            $purchaseOrder = $this->StockReceiveservice->receive($purchaseOrder, $dto);

            return response()->json($purchaseOrder);
        }
        catch (\Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], 409);
        }
        
    }
}
