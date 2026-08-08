<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository extends BaseRepository
{
    protected function model(): string
    {
        return Warehouse::class;
    }

    //every CRUD is inherited from BaseRepository
}