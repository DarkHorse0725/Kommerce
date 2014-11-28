<?php
namespace inklabs\kommerce\Entity;

class ProductQuantityDiscountTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->productQuantityDiscount = new ProductQuantityDiscount;
        $this->productQuantityDiscount->setCustomerGroup(null);
        $this->productQuantityDiscount->setType('exact');
        $this->productQuantityDiscount->setQuantity(6);
        $this->productQuantityDiscount->setValue(500);
        $this->productQuantityDiscount->setFlagApplyCatalogPromotions(false);
        $this->productQuantityDiscount->setStart(new \DateTime('2014-01-01', new \DateTimeZone('UTC')));
        $this->productQuantityDiscount->setEnd(new \DateTime('2014-12-31', new \DateTimeZone('UTC')));
    }

    public function setupExpectedView()
    {
        $reflection = new \ReflectionClass('inklabs\kommerce\Entity\View\ProductQuantityDiscount');
        $this->expectedView = $reflection->newInstanceWithoutConstructor();

        $this->expectedView->id            = $this->productQuantityDiscount->getId();
        $this->expectedView->name          = $this->productQuantityDiscount->getName();
        $this->expectedView->customerGroup = $this->productQuantityDiscount->getCustomerGroup();
        $this->expectedView->type          = $this->productQuantityDiscount->getType();
        $this->expectedView->quantity      = $this->productQuantityDiscount->getQuantity();
        $this->expectedView->value         = $this->productQuantityDiscount->getValue();
        $this->expectedView->flagApplyCatalogPromotions =
            $this->productQuantityDiscount->getFlagApplyCatalogPromotions();

        $this->expectedView->start         = $this->productQuantityDiscount->getStart();
        $this->expectedView->end           = $this->productQuantityDiscount->getEnd();
        $this->expectedView->created       = $this->productQuantityDiscount->getCreated();
        $this->expectedView->updated       = $this->productQuantityDiscount->getUpdated();
    }

    /**
     * @expectedException Exception
     */
    public function testSetName()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setName('test');
    }

    public function testGetName()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setType('exact');
        $productQuantityDiscount->setQuantity(10);
        $productQuantityDiscount->setValue(500);
        $this->assertEquals('Buy 10 or more for $5.00 each', $productQuantityDiscount->getName());

        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setType('percent');
        $productQuantityDiscount->setQuantity(10);
        $productQuantityDiscount->setValue(50);
        $this->assertEquals('Buy 10 or more for 50% off', $productQuantityDiscount->getName());

        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setType('fixed');
        $productQuantityDiscount->setQuantity(10);
        $productQuantityDiscount->setValue(500);
        $this->assertEquals('Buy 10 or more for $5.00 off', $productQuantityDiscount->getName());
    }

    public function testIsQuanityValid()
    {
        $productQuantityDiscount = new ProductQuantityDiscount;
        $productQuantityDiscount->setType('exact');
        $productQuantityDiscount->setQuantity(10);
        $productQuantityDiscount->setValue(500);

        $this->assertFalse($productQuantityDiscount->isQuantityValid(9));
        $this->assertTrue($productQuantityDiscount->isQuantityValid(10));
        $this->assertTrue($productQuantityDiscount->isQuantityValid(11));
    }
}
