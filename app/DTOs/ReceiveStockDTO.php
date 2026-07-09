<?php

namespace App\DTOs;

use Illuminate\Http\Request;



final class ReceiveStockDTO
{
    //Only things needed for receive stock is warehouse, for now
    public function __construct(public readonly int $warehouseID)
    {

    }

    public static function fromRequest(Request $request) : self
    {
        return new self(
            warehouseID: $request->integer('warehouse_id')
        );
    }
}