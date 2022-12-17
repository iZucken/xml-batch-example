<?php

namespace unit\DataObjects;

use Codeception\Test\Unit;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\ProductCategory;
use Symfony\Component\Uid\Uuid;

class DataObjectsDefinitionTest extends Unit
{
    public function testProductCategoryIsDefined()
    {
        $this->assertTrue(class_exists(ProductCategory::class));
    }

    public function testDataObjectCanBePersisted()
    {
        $category = new ProductCategory;
        $category->setParentId(1);
        $category->setKey(Uuid::v4());
        $category->setName("Tests persistence is properly wired");
        $category->save();
        $this->assertNotNull($category->getId());
        $this->assertNotNull(ProductCategory::getById($category->getId()));
    }

    public function testProductIsDefined()
    {
        $this->assertTrue(class_exists(Product::class));
    }
}
