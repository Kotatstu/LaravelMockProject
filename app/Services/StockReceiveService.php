<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class StockReceiveService
{
    public function receive(PurchaseOrder $purchaseOrder, int $warehouseID) : PurchaseOrder
    {
        //check if the order is already received and throw an exception, ortherwise, continute
        if ($purchaseOrder->status === 'received')
        {
            throw new \Exception('This purchase order has already been received.');
        }

        $purchasedOder = DB::transaction(function () use ($purchaseOrder, $warehouseID)
        {
            //loop thru the item list in the order and check if there are a current stock of it in the given warehouse ID
            foreach ($purchaseOrder->items as $item)
            {
                $stock = Stock::where('warehouse_id', $warehouseID)
                ->where('product_id', $item->product_id)
                ->lockForUpdate()//lock the concurrent database
                ->first();//return the first one found

                if (!$stock)
                {
                    $stock = Stock::create([
                        'warehouse_id' => $warehouseID,
                        'product_id' => $item->product_id,
                        'quantity' => 0,
                    ]);
                }

                $stock->increment('quantity', $item->quantity);

                
            }

            $purchaseOrder->update(['status' => 'received']);//change the single field status of whatever into received

            return $purchaseOrder->fresh(['items', 'supplier']);
        });

        return $purchasedOder;
    }
}