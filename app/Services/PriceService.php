<?php

namespace App\Services;

use App\Helpers\ArrayHelper;
use App\Repositories\CurrenciesRepository;
use App\Repositories\DiscountsRepository;
use App\Repositories\OrderProductsRepository;
use App\Repositories\ProductsRepository;
use App\Repositories\SuppliersRepository;
use Illuminate\Support\Collection;
use Mockery\Exception;

class PriceService
{
    protected SuppliersRepository $suppliersRepository;
    protected ProductsRepository $productsRepository;
    protected CurrenciesRepository $currenciesRepository;
    protected DiscountsRepository $discountsRepository;

    /**
     * @param string $repoName
     * @param array  $data
     * @param string $type
     * @return mixed
     */
    protected function getRepoByName(string $repoName, array $data, string $type): mixed
    {
        return ($data[$repoName] instanceof ('App\Repositories\\' . $type))
            ? $data[$repoName]
            : throw new Exception(class_basename($this) . ', corrupted repository "' . $repoName . '"');
    }

    public function __construct($data)
    {
        $this->discountsRepository = $this->getRepoByName('discountsRepository', $data, 'DiscountsRepository');
        $this->suppliersRepository = $this->getRepoByName('suppliersRepository', $data, 'SuppliersRepository');
        $this->productsRepository = $this->getRepoByName('productsRepository', $data, 'ProductsRepository');
        $this->currenciesRepository = $this->getRepoByName('currenciesRepository', $data, 'CurrenciesRepository');
    }

    /**
     * @param OrderProductsRepository $orderProductsRepository
     * @return Collection
     */
    public function getBestOrderSuppliers(OrderProductsRepository $orderProductsRepository): Collection
    {
        $suppliersWithProducts = $this->suppliersRepository->getSuppliersWithProducts(
            $orderProductsRepository,
            $this->discountsRepository
        );

        $allSuppliers = $this->calcOrderSummary($orderProductsRepository, $suppliersWithProducts);

        $res = [];
        foreach ($allSuppliers as $supplierId => $supplierProducts) {
            $res[$supplierId] = [
                'supplier' => $supplierId,
                'products' => $supplierProducts,
                'total' => $this->getSupplierTotal($supplierProducts),
            ];
        }

        //Best offers on top
        ArrayHelper::sortBy($res, 'total');

        return collect($res);
    }

    /**
     * @param array $supplierProducts
     * @return mixed
     */
    protected function getSupplierTotal(array $supplierProducts): mixed
    {
        $total = 0;
        foreach ($supplierProducts as $supplierProductRows) {
            foreach ($supplierProductRows as $supplierProductRow) {
                $total += $supplierProductRow['priceTotal'];
            }

        }
        return $total;
    }

    /**
     * @param OrderProductsRepository $orderProductsRepository
     * @param Collection              $suppliersWithProducts
     * @return Collection
     */
    public function calcOrderSummary(
        OrderProductsRepository $orderProductsRepository,
        Collection $suppliersWithProducts
    ): Collection {
        $res = [];
        foreach ($orderProductsRepository as $orderRow) {
            foreach ($suppliersWithProducts as $supplierId => $supplierProducts) {
                try {
                    foreach ($supplierProducts as $productId => $productDiscounts) {
                        if ($orderRow['product'] === $productId) {
                            $res[$supplierId][$productId] = $this->calcProductPrice($orderRow, $productDiscounts);
                        }
                    }
                } catch (Exception) {
                    //Do not add this supplier, it has no appropriate dividers for the order units
                }
            }
        }

        return collect($res);
    }

    /**
     * @param array $orderRow
     * @param array $productDiscounts
     * @return array
     */
    public function calcProductPrice(array $orderRow, array $productDiscounts): array
    {
        $res = [];
        $orderUnits = $orderRow['units'];
        foreach ($productDiscounts as $productDiscountId) {
            $productDiscount = $this->discountsRepository->findById($productDiscountId);

            [$orderUnits, $orderRowDivided] = $this->divideProductByUnits($orderUnits, $productDiscount);
            if (isset($orderRowDivided)) {
                $res[] = $orderRowDivided;
            }
        }

        if ($orderUnits > 0) { //example: if no price for 1 unit
            throw new Exception('Product units can\'t be divided properly.');
        }

        return $res;
    }

    /**
     * @param int   $orderUnits
     * @param array $productDiscount
     * @return array
     */
    public function divideProductByUnits(int $orderUnits, array $productDiscount): array
    {
        if ($productDiscount['units'] > $orderUnits) {
            return [$orderUnits, null]; // discount units too big for order product - skip
        }

        $fraction = $orderUnits % $productDiscount['units'];
        $whole = (int)floor($orderUnits / $productDiscount['units']);

        return [
            $fraction,
            [
                'supplier' => $productDiscount["supplier"],
                'product' => $productDiscount["product"],
                'count' => $whole,
                'units' => $productDiscount['units'],
                'price' => $productDiscount['price'],
                'unitsTotal' => $whole * $productDiscount['units'],
                'priceTotal' => $whole * $productDiscount['price'],
                'currency' => $productDiscount["currency"],
            ],
        ];
    }

}
