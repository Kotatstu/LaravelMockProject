<?php

namespace App\DTOs;

class DispatchStockDTO
{
    public function __construct(
        public readonly array $items
    ) {}

    public static function fromArray(array $validated) : self
    {
        return new self(
            items: array_map(
                fn (array $item) => DispatchStockItemDTO::fromArray($item),
                $validated['items']
            )
        );
    }
}