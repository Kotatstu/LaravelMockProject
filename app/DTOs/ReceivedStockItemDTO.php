<?php

namespace App\DTOs;

class ReceivedStockItemDTO
{
    public function __construct(
        public readonly int $purchaseOrderItemID,
        public readonly int $quantity
    ) {}

    public static function fromArray(array $item) : self
    {
        return new self(
            purchaseOrderItemID: $item['purchase_order_item_id'],
            quantity: $item['quantity']
        );
        
    }
}