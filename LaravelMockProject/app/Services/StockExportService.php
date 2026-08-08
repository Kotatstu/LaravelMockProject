<?php

namespace App\Services;

use App\DTOs\DispatchStockDTO;
use App\Models\StockExport;
use App\Models\StockExportItem;
use App\Repositories\StockExportRepository;
use App\Repositories\StockRepository;
use Illuminate\Support\Facades\DB;

class StockExportService
{
    public function __construct(
        private StockExportRepository $stockExportRepository,
        private StockRepository $stockRepository

    ) {}

    public function createWithItems(array $validated) : StockExport
    {
        $stockExport = DB::transaction(function () use ($validated)
        {
            $stockExport = $this->stockExportRepository->create($validated);
            foreach($validated['items'] as $item)
            {
                StockExportItem::create([
                    'stock_export_id' => $stockExport->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ]);
            }

            return $stockExport->load(['warehouse', 'stockExportItems']);
        });

        return $stockExport;
    }

    public function dispatch(StockExport $stockExport, DispatchStockDTO $dto) : StockExport
    {
        if($stockExport->status === 'dispatched')
        {
            throw new \Exception("This dispatch order already dispatched");
        }

        $stockExport = DB::transaction(function () use ($stockExport, $dto)
        {
            foreach($dto->items as $item)
            {
                //Get the exact StockExportItem
                $stockExportItem = StockExportItem::findOrFail($item->stockExportItemId);

                //Check matching
                if($stockExportItem->stock_export_id !== $stockExport->id)
                {
                    throw new \Exception("The product exporting not match the product planned to export");
                }

                $remaining = $stockExportItem->quantity - $stockExportItem->quantity_dispatched;
                if($item->quantity > $remaining)
                {
                    throw new \Exception("Cannot dispatch {$item->quantity} units, only {$remaining} remaining for this item");
                }

                $this->stockRepository->decrementStock($stockExport->warehouse_id, $stockExportItem->product_id, $item->quantity);
                $stockExportItem->increment('quantity_dispatched', $item->quantity);
            }
            
            $allItems = $stockExport->stockExportItems()->get();

            if($allItems->every(fn ($item) => $item->quantity === $item->quantity_dispatched))
            {
                $newStatus = "dispatched";
            }
            else $newStatus = "partly_dispatched";

            $stockExport->update(['status' => $newStatus]);

            return $stockExport->fresh(['warehouse', 'stockExportItems']);
        });

        return $stockExport;
    }
}