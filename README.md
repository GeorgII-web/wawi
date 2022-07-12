# WAWIBOX test task

## Dental Price Comparison

### Results analytics

![Results](resources/chart.png?raw=true "Results")

- Growth in the number of products in the database has minor effect
- Growth in the number of items in the order has minor effect
- Growth in the number of suppliers has minor effect
- The growth of the ratio "the number of discounts per unit of product" has significant impact and execution time degradation.
- Runtime efficiency: The upper bound of the algorithm complexity is quadratic O(n^2)
- Memory usage degrading linear O(n)

#### Limits of absolute values of the parameters 

- For 10.000 suppliers and	10.000.000 products in DB and 3 products in order:

| Discounts  |  Mem,data | Mem,alg | Mem,full | Time,Sec |
|------------|-----------|---------|----------|----------|
|  1.000.000 |       522 |     181 |      703 |    7,783 |	 
| 10.000.000 |     5.300 |   2.284 |    7.584 |  116,121 |	

- Scalable algorithm, allows growth without major changes.

#### Tests result
![Tests](resources/test.png?raw=true "Tests")

### Install

- Clone repo `git clone git@github.com:GeorgII-web/wawi.git`
- Composer install 
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```
- Start container `./vendor/bin/sail up`
- Enter container's bash `php artisan optimize:clear`

### Run

- Test `php artisan test`

### ToDo

- Data validation 
    - Order rows duplicates, minimum price.
    - Suppliers duplicates.
    - Products duplicates.
    - Discounts duplicates.
    - Units > 0.
    - Prices > 0.
    - Column types.
- Products 
    - Names differences from different suppliers   
    - Analogues and replacements
    - Different packaging, dosage, units of measurement of a similar product.
- Price 
    - Format: string, int * 100, object {price, currency}, big numbers, locale format.
    - Type: Box of 10 products 1. price for box 2. price for each * 10.
    - Different currencies.
- Units
    - Different type of units has different calculation algorithms: 
    1. Price for 10 + price for 1 = 11.  
    2. Or price for 10 used for all 11 products.     
    - ISO units: Box, Reel, Tube, Each etc. 
- Architecture    
    - Data pre-filtering from database: preferable Elasticsearch or similar search engine. Filter out suppliers that has no products or discounts from order.
    - Yeld\Chunk data execution, will reduce memory usage.
    - UX, first show list of filtered suppliers, than calculate them asynchronously one by one showing total price for order.
- Algorithm
    - Make priorities to a suppliers/products, paid users, reviews, big orders etc.
    - Different rules for a price calculating - make separate class\entity.

# Task

## Dental Price Comparison

Develop a model for a dental price comparison.

Find the best supplier for a customer and calculate the best price. The customer will input
the Product Type and the requested amount.
An order cannot be split between suppliers.

implementation is required, data is already loaded in “memory”, you can use hard coded
values.
Please implement the solution in PHP. You may use any framework but plain vanilla PHP is
also fine.

Hint: No database implementation is needed. You could pretend that the data was already
loaded into memory. But feel free to implement it if it helps you in any way. No UI is needed.

Pretend that you would build a real world application and structure your code so that
we theoretically could re-use it in a live system.

#### Supplier A

Dental Floss 1 Unit 9,00 EUR
Dental Floss 20 Units 160,00 EUR
Ibuprofen 1 Unit 5,00 EUR
Ibuprofen 10 Units 48,00 EUR

#### Supplier B

Dental Floss 1 Unit 8,00 EUR
Dental Floss 10 Units 71,00 EUR
Ibuprofen 1 Unit 6,00 EUR
Ibuprofen 5 Units 25,00 EUR
Ibuprofen 100 Units 410,00 EUR

#### Example 1
Customer wants to buy 5 Units Dental Floss and 12 Units Ibuprofen.

**Cost Supplier A:**
5 x 1 Unit Dental Floss - 45 EUR
1 x 10 Units Ibuprofen - 48 EUR
2 x 1 Unit Ibuprofen - 10 EUR
**Total: 103 EUR**

**Cost Supplier B:**
5 x 1 Unit Dental Floss - 40 EUR
2 x 5 Units Ibuprofen - 50 EUR
2 x 1 Unit Ibuprofen - 12 EUR
**Total: 102 EUR**

**Result: Supplier B is cheaper - 102 EUR**

#### Example 2
Customer wants to buy 105 Units Ibuprofen

**Cost Supplier A:**
10 x 10 Units Ibuprofen - 480 EUR
5 x 1 Unit Ibuprofen - 25 EUR
**Total: 505 EUR**

**Cost Supplier B:**
1 x 100 Units Ibuprofen - 410 EUR
1 x 5 Units Ibuprofen - 25 EUR
**Total: 435 EUR**

**Result: Supplier B is cheaper - 435 EUR**
