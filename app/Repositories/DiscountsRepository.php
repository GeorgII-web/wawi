<?php

namespace App\Repositories;

use App\Helpers\ArrayHelper;

class DiscountsRepository extends MainRepository
{
    protected array $dataFormat = [
        'supplier',
        'product',
        'units',
        'price',
        'currency',
    ];

    public function __construct(array $items = [])
    {
        $this->validateArray($items);

        //prepare discounts - sort it, big units first
        ArrayHelper::sortBy($items, 'units', SORT_DESC);

        parent::__construct($items);
    }
}
