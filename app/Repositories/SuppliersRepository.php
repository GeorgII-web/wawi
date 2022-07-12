<?php
namespace App\Repositories;


use Illuminate\Support\Collection;

class SuppliersRepository extends MainRepository
{
    protected array $dataFormat = [
        'name',
    ];

    /**
     * Get suppliers with needed products.
     * todo move to SQL/ElasticSearch aggregation
     *
     * @param OrderProductsRepository $orderProductsRepository
     * @param DiscountsRepository     $discountsRepository
     * @return Collection
     */
    public function getSuppliersWithProducts(
        OrderProductsRepository $orderProductsRepository,
        DiscountsRepository $discountsRepository
    ): Collection
    {
        $suppliers = [];
        // Get each row of an order
        foreach ($orderProductsRepository as $orderProduct) {
            // Get each row of a discounts
            foreach ($discountsRepository as $discountId => $discount) {
                if ($orderProduct['product'] === $discount['product']) {
                    // Mark products in supplier
                    $suppliers[$discount['supplier']][$discount['product']][$discountId] = $discountId;
                }
            }
        }

        $res = [];
        // Take only suppliers that have all products
        foreach ($suppliers as $supplierId => $supplierProducts) {
            if ($this->isSupplierHasAllOrderProducts($orderProductsRepository, $supplierProducts)) {
                $res[$supplierId] = $supplierProducts;
            }
        }

        return collect($res);
    }

    /**
     * @param OrderProductsRepository $orderProductsRepository
     * @param array                   $supplierProducts
     * @return bool
     */
    public function isSupplierHasAllOrderProducts(
        OrderProductsRepository $orderProductsRepository,
        array $supplierProducts): bool
    {
        if (count($supplierProducts) === 0 || $orderProductsRepository->count() === 0) {
            return false;
        }

        //Each order product
        foreach ($orderProductsRepository->toArray() as $orderRow) {
            //Each supplier product
            foreach (array_keys($supplierProducts) as $supplierProductId) {
                if ($orderRow['product'] === $supplierProductId) {
                    $flag = true;
                    break;
                }
                $flag = false;
            }
            // If supplier hasn't any of these products, then it's not valid supplier
            if (!$flag) {
                return false;
            }
        }

        return true;
    }
}
