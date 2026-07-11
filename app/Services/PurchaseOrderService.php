<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Repositories\PurchaseOrderRepository;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(private PurchaseOrderRepository $repository)
    {
    }

    public function createWithItems(array $validated) : PurchaseOrder
    {
        $purchaseOrder = DB::transaction(function () use ($validated)
        {
            $purchaseOrder = $this->repository->create($validated);
            foreach($validated['items'] as $item)
            {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id, 
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ]);
            }

            return $purchaseOrder->load(['supplier', 'items']);
        });

        return $purchaseOrder;
    }
}