<?php

namespace App\Repositories;

class OrderProductsRepository extends MainRepository
{
    protected array $dataFormat = [
        'product',
        'units',
    ];
}
