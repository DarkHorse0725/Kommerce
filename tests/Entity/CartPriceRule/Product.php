<?php
namespace inklabs\kommerce\Entity\CartPriceRule;

use inklabs\kommerce\Entity as Entity;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $priceRule = new Product(new Entity\Product, 1);
        $this->assertInstanceOf('inklabs\kommerce\Entity\Product', $priceRule->getProduct());
    }

    /**
     * @return Entity\Product
     */
    private function getProduct($id)
    {
        $product = new Entity\Product;
        $product->setid($id);
        return $product;
    }

    public function testMathces()
    {
        $product = $this->getProduct(1);

        $cartItem = new Entity\CartItem($product, 1);
        $priceRule = new Product($product, 1);

        $this->assertTrue($priceRule->matches($cartItem));
    }

    public function testMatchesWithLargerQuantity()
    {
        $product = $this->getProduct(1);

        $cartItem = new Entity\CartItem($product, 2);
        $priceRule = new Product($product, 1);

        $this->assertTrue($priceRule->matches($cartItem));
    }

    public function testMatchReturnsFalse()
    {
        $product1 = $this->getProduct(1);
        $product2 = $this->getProduct(2);

        $cartItem = new Entity\CartItem($product1, 1);
        $priceRule = new Product($product2, 1);

        $this->assertFalse($priceRule->matches($cartItem));
    }

    public function testProductDoesNotMatchByQuantity()
    {
        $product1 = $this->getProduct(1);

        $cartItem = new Entity\CartItem($product1, 1);
        $priceRule = new Product($product1, 2);

        $this->assertFalse($priceRule->matches($cartItem));
    }
}
