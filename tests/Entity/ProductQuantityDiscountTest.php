<?php
namespace inklabs\kommerce\Entity;

use DateTime;
use inklabs\kommerce\Exception\BadMethodCallException;
use inklabs\kommerce\Lib\Pricing;
use inklabs\kommerce\tests\Helper\DoctrineTestCase;

class ProductQuantityDiscountTest extends DoctrineTestCase
{
    public function testCreate()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setCustomerGroup(null);
        $productQuantityDiscount->setQuantity(6);
        $productQuantityDiscount->setFlagApplyCatalogPromotions(true);
        $productQuantityDiscount->setProduct(new Product);

        $this->assertEntityValid($productQuantityDiscount);
        $this->assertSame(null, $productQuantityDiscount->getCustomerGroup());
        $this->assertSame(6, $productQuantityDiscount->getQuantity());
        $this->assertSame(true, $productQuantityDiscount->getFlagApplyCatalogPromotions());
        $this->assertTrue($productQuantityDiscount->getProduct() instanceof Product);
    }

    public function testSetNameThrowsException()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;

        $this->setExpectedException(
            BadMethodCallException::class,
            'Unable to set name'
        );

        $productQuantityDiscount->setName('test');
    }

    public function testIsQuantityValid()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setQuantity(5);
        $this->assertTrue($productQuantityDiscount->isQuantityValid(6));
    }

    public function testIsQuantityValidReturnsFalse()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setQuantity(5);
        $this->assertFalse($productQuantityDiscount->isQuantityValid(4));
    }

    public function testIsValid()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setQuantity(5);
        $this->assertTrue($productQuantityDiscount->isValid(new DateTime, 6));
    }

    public function testGetNameExact()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setType(AbstractPromotion::TYPE_EXACT);
        $productQuantityDiscount->setQuantity(10);
        $productQuantityDiscount->setValue(500);
        $this->assertSame('Buy 10 or more for $5.00 each', $productQuantityDiscount->getName());
    }

    public function testGetNamePercent()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setType(AbstractPromotion::TYPE_PERCENT);
        $productQuantityDiscount->setQuantity(10);
        $productQuantityDiscount->setValue(50);
        $this->assertSame('Buy 10 or more for 50% off', $productQuantityDiscount->getName());
    }

    public function testGetNameFixed()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setType(AbstractPromotion::TYPE_FIXED);
        $productQuantityDiscount->setQuantity(10);
        $productQuantityDiscount->setValue(500);
        $this->assertSame('Buy 10 or more for $5.00 off', $productQuantityDiscount->getName());
    }

    public function testGetPrice()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setProduct(new Product);
        $productQuantityDiscount->setQuantity(1);
        $this->assertTrue($productQuantityDiscount->getPrice(new Pricing) instanceof Price);
    }
}
