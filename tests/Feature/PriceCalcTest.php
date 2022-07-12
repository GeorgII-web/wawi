<?php

namespace Tests\Feature;

use App\Repositories\DiscountsRepository;
use App\Repositories\OrderProductsRepository;
use App\Repositories\ProductsRepository;
use App\Repositories\SuppliersRepository;
use App\Repositories\CurrenciesRepository;
use App\Services\PriceService;
use Exception;
use Tests\TestCase;

class PriceCalcTest extends TestCase
{
    protected SuppliersRepository $suppliersRepository;
    protected ProductsRepository $productsRepository;
    protected CurrenciesRepository $currenciesRepository;
    protected PriceService $priceService;
    protected OrderProductsRepository $orderProductsRepository;
    protected DiscountsRepository $discountsRepository;

    protected function setUp(): void
    {
        $this->suppliersRepository = new SuppliersRepository([
            'id0' => [
                'name' => 'Supplier A',
            ],
            'id1' => [
                'name' => 'Supplier B',
            ],
            'id2' => [
                'name' => 'Supplier TEST',
            ],
        ]);

        $this->productsRepository = new ProductsRepository([
            'id0' => [
                'name' => 'Dental Floss',
            ],
            'id1' => [
                'name' => 'Ibuprofen',
            ],
        ]);

        $this->currenciesRepository = new CurrenciesRepository([
            'id0' => [
                'name' => 'EUR',
            ],
            'id1' => [
                'name' => 'USD',
            ],
        ]);

        parent::setUp();
    }

    public function test_example_1(): void
    {
        $this->orderProductsRepository = new OrderProductsRepository([
            'id0' => [
                'product' => 'id0',
                'units' => 5,
            ],
            'id1' => [
                'product' => 'id1',
                'units' => 12,
            ],
        ]);
        $this->discountsRepository = new DiscountsRepository([
            'id0' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 1,
                'price' => 9,
                'currency' => 'id0',
            ],
            'id1' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 20,
                'price' => 160,
                'currency' => 'id0',
            ],
            'id2' => [
                'supplier' => 'id0',
                'product' => 'id1',
                'units' => 1,
                'price' => 5,
                'currency' => 'id0',
            ],
            'id3' => [
                'supplier' => 'id0',
                'product' => 'id1',
                'units' => 10,
                'price' => 48,
                'currency' => 'id0',
            ],

            'id4' => [
                'supplier' => 'id1',
                'product' => 'id0',
                'units' => 1,
                'price' => 8,
                'currency' => 'id0',
            ],
            'id5' => [
                'supplier' => 'id1',
                'product' => 'id0',
                'units' => 10,
                'price' => 71,
                'currency' => 'id0',
            ],
            'id6' => [
                'supplier' => 'id1',
                'product' => 'id1',
                'units' => 1,
                'price' => 6,
                'currency' => 'id0',
            ],
            'id7' => [
                'supplier' => 'id1',
                'product' => 'id1',
                'units' => 5,
                'price' => 25,
                'currency' => 'id0',
            ],
            'id8' => [
                'supplier' => 'id1',
                'product' => 'id1',
                'units' => 100,
                'price' => 410,
                'currency' => 'id0',
            ],
        ]);

        $this->priceService = new PriceService([
            'discountsRepository' => $this->discountsRepository,
            'suppliersRepository' => $this->suppliersRepository,
            'productsRepository' => $this->productsRepository,
            'currenciesRepository' => $this->currenciesRepository,
        ]);

        $bestSuppliers = $this->priceService->getBestOrderSuppliers($this->orderProductsRepository);

        $bestSupplier = $bestSuppliers->first();

        $this->assertEquals('102', $bestSupplier['total']);
        $this->assertEquals('id1', $bestSupplier['supplier']);
        $this->assertCount('2', $bestSupplier['products']);
    }

    public function test_example_2(): void
    {
        $this->orderProductsRepository = new OrderProductsRepository([
            'id0' => [
                'product' => 'id1',
                'units' => 105,
            ],
        ]);
        $this->discountsRepository = new DiscountsRepository([
            'id0' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 1,
                'price' => 9,
                'currency' => 'id0',
            ],
            'id1' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 20,
                'price' => 160,
                'currency' => 'id0',
            ],
            'id2' => [
                'supplier' => 'id0',
                'product' => 'id1',
                'units' => 1,
                'price' => 5,
                'currency' => 'id0',
            ],
            'id3' => [
                'supplier' => 'id0',
                'product' => 'id1',
                'units' => 10,
                'price' => 48,
                'currency' => 'id0',
            ],

            'id4' => [
                'supplier' => 'id1',
                'product' => 'id0',
                'units' => 1,
                'price' => 8,
                'currency' => 'id0',
            ],
            'id5' => [
                'supplier' => 'id1',
                'product' => 'id0',
                'units' => 10,
                'price' => 71,
                'currency' => 'id0',
            ],
            'id6' => [
                'supplier' => 'id1',
                'product' => 'id1',
                'units' => 1,
                'price' => 6,
                'currency' => 'id0',
            ],
            'id7' => [
                'supplier' => 'id1',
                'product' => 'id1',
                'units' => 5,
                'price' => 25,
                'currency' => 'id0',
            ],
            'id8' => [
                'supplier' => 'id1',
                'product' => 'id1',
                'units' => 100,
                'price' => 410,
                'currency' => 'id0',
            ],
        ]);

        $this->priceService = new PriceService([
            'discountsRepository' => $this->discountsRepository,
            'suppliersRepository' => $this->suppliersRepository,
            'productsRepository' => $this->productsRepository,
            'currenciesRepository' => $this->currenciesRepository,
        ]);

        $bestSuppliers = $this->priceService->getBestOrderSuppliers($this->orderProductsRepository);

        $bestSupplier = $bestSuppliers->first();

        $this->assertEquals('435', $bestSupplier['total']);
        $this->assertEquals('id1', $bestSupplier['supplier']);
        $this->assertCount('1', $bestSupplier['products']);
    }

    public function test_divide_units_134_on_3_rows(): void
    {
        $this->orderProductsRepository = new OrderProductsRepository([
            'id0' => [
                'product' => 'id0',
                'units' => 134,
            ],
        ]);
        $this->discountsRepository = new DiscountsRepository([
            'id0' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 1,
                'price' => 9,
                'currency' => 'id0',
            ],
            'id1' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 20,
                'price' => 160,
                'currency' => 'id0',
            ],
            'id2' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 100,
                'price' => 700,
                'currency' => 'id0',
            ],

        ]);

        $this->priceService = new PriceService([
            'discountsRepository' => $this->discountsRepository,
            'suppliersRepository' => $this->suppliersRepository,
            'productsRepository' => $this->productsRepository,
            'currenciesRepository' => $this->currenciesRepository,
        ]);

        $bestSuppliers = $this->priceService->getBestOrderSuppliers($this->orderProductsRepository);

        $bestSupplier = $bestSuppliers->first();

        $this->assertEquals('986', $bestSupplier['total']);
        $this->assertEquals('id0', $bestSupplier['supplier']);
        $this->assertCount('3', $bestSupplier['products']['id0']);
    }

    public function test_no_suppliers(): void
    {
        $this->orderProductsRepository = new OrderProductsRepository([
            'id0' => [
                'product' => 'id0',
                'units' => 55,
            ],
        ]);
        $this->discountsRepository = new DiscountsRepository([
            'id0' => [
                'supplier' => 'id0',
                'product' => 'id0',
                'units' => 33,
                'price' => 9,
                'currency' => 'id0',
            ],
        ]);

        $this->priceService = new PriceService([
            'discountsRepository' => $this->discountsRepository,
            'suppliersRepository' => $this->suppliersRepository,
            'productsRepository' => $this->productsRepository,
            'currenciesRepository' => $this->currenciesRepository,
        ]);

        $bestSuppliers = $this->priceService->getBestOrderSuppliers($this->orderProductsRepository);

        $this->assertEquals(true, $bestSuppliers->isEmpty());
    }

    /**
     * @throws Exception
     */
    public function test_performance_measurement(): void
    {
        $suppliersCount = 500;
        $productsCount = 100000;
        $discountsCount = 200000;

        $this->orderProductsRepository = new OrderProductsRepository([
            'id0' => [
                'product' => 'id0',
                'units' => 555,
            ],
            'id1' => [
                'product' => 'id1',
                'units' => 777,
            ],
            'id2' => [
                'product' => 'id1',
                'units' => 888,
            ],
        ]);

        $discounts = [];
        for ($i = 0; $i < $discountsCount; $i++) {
            $discounts['id' . $i] = [
                'supplier' => 'id' . random_int(0, $suppliersCount),
                'product' => 'id' . random_int(0, $productsCount),
                'units' => random_int(1, $i + 1),
                'price' => 1,
                'currency' => 'id0',
            ];
        }

        $memoryStart = round(memory_get_peak_usage() / 1024 / 1024);
        $timeStart = microtime(true);

        $this->discountsRepository = new DiscountsRepository($discounts);

        $this->priceService = new PriceService([
            'discountsRepository' => $this->discountsRepository,
            'suppliersRepository' => $this->suppliersRepository,
            'productsRepository' => $this->productsRepository,
            'currenciesRepository' => $this->currenciesRepository,
        ]);

        $this->priceService->getBestOrderSuppliers($this->orderProductsRepository);

        $time = round((microtime(true) - $timeStart) * 1000) / 1000;
        $memory = round(memory_get_peak_usage() / 1024 / 1024);
//        dd($memoryStart . ' mb, ' . $memory . ' mb, ' . $time . ' sec');
        $this->assertLessThan('200', $memory);
        $this->assertLessThan('10', $time);
    }
}
