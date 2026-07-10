<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository extends BaseRepository
{
    public function model(): string
    {
        return Warehouse::class;
    }

    //every CRUD is inherited from BaseRepository
}