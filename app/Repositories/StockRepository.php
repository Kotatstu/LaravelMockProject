<?php

namespace App\Repositories;

use App\Models\Stock;

class StockRepository extends BaseRepository
{
    public function model() : string
    {
        return Stock::class;
    }

    public function incrementStock(int $warehouseID, int $productID, int $quantity) : Stock
    {
       $stock = Stock::where('warehouse_id', $warehouseID)
       ->where('product_id', $productID)
       ->lockForUpdate()
       ->first();

       if(!$stock)
       {
        $stock = Stock::create([
            'warehouse_id'=> $warehouseID,
            'product_id' => $productID,
            'quantity' => 0
        ]);
       }

       $stock->increment('quantity', $quantity);

       return $stock;
    }
}
