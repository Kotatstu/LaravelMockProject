<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ReceiveStockDTO;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Services\StockReceiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct(private StockReceiveService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchase = PurchaseOrder::with(['supplier', 'items'])->get();

        return response()->json($purchase);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'reference' => ['required', 'string', 'unique:purchase_orders,reference'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'status' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1']
        ]);

        $purchase = DB::transaction(function () use ($validate)
        {
            $purchaseOrder = PurchaseOrder::create($validate);
            foreach ($validate['items'] as $item)
            {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ]);
            }

            return $purchaseOrder;
        });

        return response()->json($purchase->load(['items', 'supplier']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        return response()->json($purchaseOrder->load(['supplier', 'items.product']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validate = $request->validate([
            'status' => ['sometimes', 'string']
        ]);

        $purchaseOrder->update($validate);

        return response()->json($purchaseOrder->load(['supplier', 'items']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return response()->json(null, 204);
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id']
        ]);
        
        try
        {
            $dto = ReceiveStockDTO::fromRequest($request);
            $purchaseOrder = $this->service->receive($purchaseOrder, $dto->warehouseID);

            return response()->json($purchaseOrder);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ],
            409);
        }
        
    }
}
