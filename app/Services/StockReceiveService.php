<?php

namespace App\Services;

use App\DTOs\ReceiveStockDTO;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Repositories\StockRepository;
use Illuminate\Support\Facades\DB;

class StockReceiveService
{
    public function __construct(private StockRepository $stockRepository)
    {        
    }


    // Buniness logic:
    // 1: Guard -> If status is pending/partly_received then continue, else throw exception
    // 2: Loop thru dto['items], check each purchase_order_item_id and quantity, check if that item belong to the current purchase order
    // + Calculate the remainging quantities, and then check if the incomming item quantity equal or less than the remaining if not throw expception
    //3: Increase the quantity received and the stock quantity
    //4: Check the entire purchaseOrder, every items, if quantity == quantity received => change status to received, if a single item quantity received > 0 => status change to receive_partly
    //5: return
    public function receive(PurchaseOrder $purchaseOrder, ReceiveStockDTO $dto) : PurchaseOrder
    {
        //check if the order is already received and throw an exception, ortherwise, continute
        if ($purchaseOrder->status === 'received')
        {
            throw new \Exception('This purchase order has already been received.');
        }

        $purchasedOder = DB::transaction(function () use ($purchaseOrder, $dto)
        {
            //loop thru the item list in the order and check if there are a current stock of it in the given warehouse ID
            foreach ($dto->items as $item)
            {
                //Tìm đến đúng cái purchaseOrderItem chứa cái product đó, không cần biết productID, tìm bằng khóa chính của purchaseOrderItem,
                //client gửi một cặp ID khóa chính và số lượng cho mỗi một item trong items
                $purchaseOrderItem = PurchaseOrderItem::findOrFail($item->purchaseOrderItemID);

                //Check if the product received matching the purchase order, if not throw exception
                if($purchaseOrder->id !== $purchaseOrderItem->purchase_order_id)
                {
                    throw new \Exception("The product receive does not match the product ordered");
                }

                //Get the missing quantity
                $remaining = $purchaseOrderItem->quantity - $purchaseOrderItem->quantity_received;

                //Check if the curren received item quantity equal or less than the remaining missing quanity, if yes proceed, if not throw exception
                if($item->quantity > $remaining)
                {
                    throw new \Exception("Cannot receive {$item->quantity} unit - only {$remaining} for this item");
                }

                //increase curren item stored in the stock
                $this->stockRepository->incrementStock($dto->warehouseID, $purchaseOrderItem->product_id, $item->quantity);

                //increase the quantity received in the purchaseOrderItem
                $purchaseOrderItem->increment('quantity_received', $item->quantity);
            }

            //Recalalte the curren purchaseOrder status, if quantity = quantity_received FOR EVERY ITEM, change status to "received",
            //If a single item in this order > 0, change status to partly_received

            //Get all the relations purchaseOrderItem
            $allItems = $purchaseOrder->items()->get();

            //return true if every single item is true
            if($allItems->every(fn ($item) => $item->quantity_received >= $item->quantity) )
            {
                $newStatus = 'received';
            }
            else $newStatus = 'partly_received';

            $purchaseOrder->update(['status' => $newStatus]);

            return $purchaseOrder->fresh(['items', 'supplier']);
        });

        return $purchasedOder;
    }
}