<?php

namespace App\Repositories;

use App\Models\Stock;

class StockRepository extends BaseRepository
{
    protected function model() : string
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

    public function decrementStock(int $warehouseID, int $productID, int $quantity) : Stock
    {
        //find the matching record
        $stock = Stock::where('warehouse_id', $warehouseID)
        ->where('product_id', $productID)
        ->lockForUpdate()
        ->first();

        //If not found ORRRRR the current item count in stock is not enuf for exporting
        if(!$stock || $stock->quantity < $quantity)
        {
            throw new \Exception("Insufficient stock available for this product");
        }

        $stock->decrement('quantity', $quantity);

        return $stock;
    }
}
