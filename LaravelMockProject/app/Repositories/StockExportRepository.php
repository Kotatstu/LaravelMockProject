<?php

namespace App\Repositories;

use App\Models\StockExport;

class StockExportRepository extends BaseRepository
{
    protected function model() : string
    {
        return StockExport::class;
    }
}