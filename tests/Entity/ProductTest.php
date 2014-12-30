<?php
namespace inklabs\kommerce\Entity;

use inklabs\kommerce\Service as Service;
use Symfony\Component\Validator\Validation;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $product = new Product;
        $product->setId(1);
        $product->setSku('TST101');
        $product->setName('Test Product');
        $product->setUnitPrice(500);
        $product->setQuantity(10);
        $product->setIsInventoryRequired(true);
        $product->setIsPriceVisible(true);
        $product->setIsActive(true);
        $product->setIsVisible(true);
        $product->setIsTaxable(true);
        $product->setIsShippable(true);
        $product->setShippingWeight(16);
        $product->setDescription('Test description');
        $product->setRating(500);
        $product->setDefaultImage('http://lorempixel.com/400/200/');
        $product->addTag(new Tag);
        $product->addImage(new Image);
        $product->addProductQuantityDiscount(new ProductQuantityDiscount);

        $validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        $this->assertEmpty($validator->validate($product));
        $this->assertEquals(1, $product->getId());
        $this->assertEquals('TST101', $product->getSku());
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(500, $product->getUnitPrice());
        $this->assertEquals(10, $product->getQuantity());
        $this->assertEquals(true, $product->getIsInventoryRequired());
        $this->assertEquals(true, $product->getIsPriceVisible());
        $this->assertEquals(true, $product->getIsActive());
        $this->assertEquals(true, $product->getIsVisible());
        $this->assertEquals(true, $product->getIsTaxable());
        $this->assertEquals(true, $product->getIsShippable());
        $this->assertEquals(16, $product->getShippingWeight());
        $this->assertEquals('Test description', $product->getDescription());
        $this->assertEquals(5, $product->getRating());
        $this->assertEquals('http://lorempixel.com/400/200/', $product->getDefaultImage());
        $this->assertTrue($product->getTags()[0] instanceof Tag);
        $this->assertTrue($product->getImages()[0] instanceof Image);
        $this->assertTrue($product->getProductQuantityDiscounts()[0] instanceof ProductQuantityDiscount);
        $this->assertTrue($product->getView() instanceof View\Product);
    }

    public function testInStock()
    {
        $product = new Product;
        $product->setIsInventoryRequired(true);
        $product->setQuantity(5);
        $this->assertTrue($product->inStock());
    }

    public function testInStockWithoutInventoryRequired()
    {
        $product = new Product;
        $product->setIsInventoryRequired(false);
        $this->assertTrue($product->inStock());
    }

    public function testInStockReturnsFalseWhenLackingQuantity()
    {
        $product = new Product;
        $product->setIsInventoryRequired(true);
        $product->setQuantity(0);
        $this->assertFalse($product->inStock());
    }

    public function testGetPrice()
    {
        $product = new Product;
        $product->setQuantity(1);
        $this->assertTrue($product->getPrice(new Service\Pricing) instanceof Price);
    }
}
