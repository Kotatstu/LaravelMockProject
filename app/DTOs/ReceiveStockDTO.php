<?php

namespace App\DTOs;

final class ReceiveStockDTO
{
    //Only things needed for receive stock is warehouse, for now
    public function __construct(
        public readonly int $warehouseID,
        public readonly array $items
        ) {}

    public static function fromArray(array $validated) : self
    {
        return new self(
            warehouseID: $validated['warehouse_id'],
            items: array_map(
                fn (array $item) => ReceivedStockItemDTO::fromArray($item),
                $validated['items']
            )//array_map use function and apply it to every item in items transform every json array into the DTO
        );
    }
}