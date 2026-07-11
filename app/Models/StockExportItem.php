<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockExportItem extends Model
{
    protected $fillable = ['stock_export_id', 'product_id', 'quantity', 'quantity_dispatched'];

    public function stockExport()
    {
        return $this->belongsTo(StockExport::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
