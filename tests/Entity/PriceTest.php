<?php
namespace inklabs\kommerce\Entity;

use inklabs\kommerce\tests\Helper\DoctrineTestCase;

class PriceTest extends DoctrineTestCase
{
    public function testCreate()
    {
        $price = new Price;
        $price->origUnitPrice = 2500;
        $price->unitPrice = 1750;
        $price->origQuantityPrice = 2500;
        $price->quantityPrice = 1750;
        $price->addCatalogPromotion(new CatalogPromotion);
        $price->addProductQuantityDiscount(new ProductQuantityDiscount);

        $this->assertEntityValid($price);
        $this->assertTrue($price->getCatalogPromotions()[0] instanceof CatalogPromotion);
        $this->assertTrue($price->getProductQuantityDiscounts()[0] instanceof ProductQuantityDiscount);
    }

    public function testAdd()
    {
        $one = new Price;
        $one->unitPrice         = 1;
        $one->origUnitPrice     = 1;
        $one->quantityPrice     = 1;
        $one->origQuantityPrice = 1;

        $two = new Price;
        $two->unitPrice         = 2;
        $two->origUnitPrice     = 2;
        $two->quantityPrice     = 2;
        $two->origQuantityPrice = 2;

        $three = new Price;
        $three->unitPrice         = 3;
        $three->origUnitPrice     = 3;
        $three->quantityPrice     = 3;
        $three->origQuantityPrice = 3;

        $this->assertEquals($three, Price::add($one, $two));
    }
}
