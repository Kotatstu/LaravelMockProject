<?php

namespace App\DTOs;

class DispatchStockItemDTO
{
    public function __construct(
        public readonly int $stockExportItemId,
        public readonly int $quantity,
    ) {}

    public static function fromArray(array $item) : self
    {
        return new self(
            stockExportItemId: $item['stock_export_item_id'],
            quantity: $item['quantity']
        );
    }
}