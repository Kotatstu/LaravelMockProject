<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository extends BaseRepository
{
    protected function model() : string
    {
        return Supplier::class;
    }
}