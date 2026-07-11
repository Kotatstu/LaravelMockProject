<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockExport extends Model
{
    protected $fillable = ['reference', 'warehouse_id', 'destination', 'status'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockExportItems()
    {
        return $this->hasMany(StockExportItem::class);
    }
}
