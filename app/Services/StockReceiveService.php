<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Repositories\StockRepository;
use Illuminate\Support\Facades\DB;

class StockReceiveService
{
    public function __construct(private StockRepository $stockRepository)
    {        
    }

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
                $this->stockRepository->incrementStock($warehouseID, $item->product_id, $item->quantity);
            }

            $purchaseOrder->update(['status' => 'received']);//change the single field status of whatever into received

            return $purchaseOrder->fresh(['items', 'supplier']);
        });

        return $purchasedOder;
    }
}