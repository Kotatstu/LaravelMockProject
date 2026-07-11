<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;

class PurchaseOrderRepository extends BaseRepository
{
    public function model(): string
    {
        return PurchaseOrder::class;
    }

}