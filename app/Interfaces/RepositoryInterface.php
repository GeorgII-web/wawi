<?php

namespace App\Interfaces;

interface RepositoryInterface
{
    public function validateArray(array $array): array;

    public function findById(string $Id): ?array;
}
