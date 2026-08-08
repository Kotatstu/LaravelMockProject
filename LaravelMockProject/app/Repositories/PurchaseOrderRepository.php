<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;

class PurchaseOrderRepository extends BaseRepository
{
    protected function model(): string
    {
        return PurchaseOrder::class;
    }

}