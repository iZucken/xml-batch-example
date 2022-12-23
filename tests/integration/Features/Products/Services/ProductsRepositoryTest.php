<?php

namespace integration\Features\Products\Services;

use App\Features\Products\DTO\ProductsIndexQuery;
use App\Features\Products\Services\ProductsRepository;
use Codeception\Test\Unit;
use Doctrine\DBAL\Connection;
use Pimcore\Tests\Helper\Pimcore;

/**
 * @coversDefaultClass \App\Features\Products\Services\ProductsRepository
 */
class ProductsRepositoryTest extends Unit
{
    private ProductsRepository $repository;
    private Connection $db;

    public function setUp(): void
    {
        parent::setUp();
        /** @var Pimcore $pimcore */
        $pimcore = $this->getModule('\Pimcore\Tests\Helper\Pimcore');
        $this->db = $pimcore->grabService(Connection::class);
        $this->repository = new ProductsRepository($this->db);
    }

    /**
     * @covers ::__construct
     * @covers ::getAllCategoryNames
     */
    public function testGetAllCategoryNames()
    {
        $this->db->executeStatement("INSERT INTO products (name, weight, category, absolute_weight, description_common, description_for_ozon, description_for_wildberries)
        VALUES ('Product 1', '1 g', 'Category A', 1, 'test', '', '')
        , ('Product 2', '1 g', 'Category A', 2, '', 'test', '')
        , ('Product 3', '1 g', 'Category A', 3, '', '', 'test')
        , ('Product 4', '1 g', 'Category A', 4, '', '', '')
        , ('Product 5', '1 g', 'Category A', 5, '', '', '')
        , ('Product 6', '1 kg', 'Category B', 1000, '', '', '')
        , ('Product 7', '2 kg', 'Category B', 2000, '', '', '')
        , ('Product 8', '3 kg', 'Category B', 3000, '', '', '')
        , ('Product 9', '4 kg', 'Category B', 4000, '', '', '')
        , ('Product 10', '5 kg', 'Category B', 5000, '', '', '')");
        $this->assertEquals([
            'Category A',
            'Category B',
        ], $this->repository->getAllCategoryNames());
    }

    /**
     * @covers ::getTotalProducts
     * @depends testGetAllCategoryNames
     */
    public function testGetTotalProducts()
    {
        $this->assertEquals(10, $this->repository->getTotalProducts());
    }

    /**
     * @covers ::indexByQuery
     * @depends testGetTotalProducts
     */
    public function testIndexByQuery()
    {
        $index = $this->repository->indexByQuery(new ProductsIndexQuery(5, 0, 'category', 'desc'));
        $this->assertCount(5, $index['list']);
        $this->assertEquals(10, $index['filtered']);
        $this->assertEquals('Category B', $index['list'][0]['category']);
        $index = $this->repository->indexByQuery(new ProductsIndexQuery(
            10, 0, 'name', 'desc', 'test'
        ));
        $this->assertCount(3, $index['list']);
        $this->assertEquals(3, $index['filtered']);
        $this->assertEquals('Product 3', $index['list'][0]['name']);
        $index = $this->repository->indexByQuery(new ProductsIndexQuery(
            10, 0, 'weight', 'desc'
        ));
        $this->assertEquals('5 kg', $index['list'][0]['weight']);
        $this->assertEquals('1 g', $index['list'][9]['weight']);
        $index = $this->repository->indexByQuery(new ProductsIndexQuery(
            10, 0, null, null, null, 'Category B'
        ));
        $this->assertCount(5, $index['list']);
        $this->assertEquals(5, $index['filtered']);
    }
}
