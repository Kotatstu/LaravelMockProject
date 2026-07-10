<?php

namespace App\DTOs;

final class ReceiveStockDTO
{
    //Only things needed for receive stock is warehouse, for now
    public function __construct(public readonly int $warehouseID)
    {

    }

    public static function fromArray(array $validated) : self
    {
        return new self(
            warehouseID: $validated['warehouse_id'],
        );
    }
}