<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Illuminate\Support\Collection;
use RuntimeException;

class MainRepository extends Collection implements RepositoryInterface
{
    /**
     * @var array Array of strings, list of fields.
     */
    protected array $dataFormat = [];

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->validateArray($items);

        parent::__construct($items);
    }

    /**
     * @param array $array
     * @return array
     */
    public function validateArray(array $array): array
    {
        foreach ($array as $key => $item) {
            if (!is_string($key) || !str_starts_with($key, 'id')) {
                throw new RuntimeException('Repository "' . class_basename($this) . '" IDs must be of string type and starts from "id"');
            }
            foreach ($this->dataFormat as $dataFormatKey) {
                if (!isset($item[$dataFormatKey])) {
                    throw new RuntimeException(class_basename($this) . ' bad format, id "' . $key . '", field "' . $dataFormatKey . '".');
                }
            }
        }

        return $array;
    }

    /**
     * @param string $Id
     * @return array|null
     */
    public function findById(string $Id): ?array
    {
        return $this->toArray()[$Id] ?? null;
    }
}
