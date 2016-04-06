<?php
namespace inklabs\kommerce\Entity;

use inklabs\kommerce\Lib\Pricing;
use inklabs\kommerce\tests\Helper\DoctrineTestCase;

class CartItemTest extends DoctrineTestCase
{
    public function testCreate()
    {
        $cartItem = $this->dummyData->getCartItemFull();

        $pricing = new Pricing;

        $this->assertEntityValid($cartItem);
        $this->assertTrue($cartItem instanceof CartItem);
        $this->assertSame(2, $cartItem->getQuantity());
        $this->assertSame('P1-OP1-OV1', $cartItem->getFullSku());
        $this->assertSame(600, $cartItem->getPrice($pricing)->quantityPrice);
        $this->assertSame(60, $cartItem->getShippingWeight());
        $this->assertTrue($cartItem->getCartItemOptionProducts()[0] instanceof CartItemOptionProduct);
        $this->assertTrue($cartItem->getCartItemOptionValues()[0] instanceof CartItemOptionValue);
        $this->assertTrue($cartItem->getCartItemTextOptionValues()[0] instanceof CartItemTextOptionValue);
        $this->assertTrue($cartItem->getPrice($pricing) instanceof Price);
        $this->assertTrue($cartItem->getCart() instanceof Cart);
    }

    public function testClone()
    {
        $cartItem = $this->dummyData->getCartItemFull();
        $newCartItem = clone $cartItem;

        $this->assertNotSame($cartItem, $newCartItem);

        $this->assertNotSame(
            $cartItem->getCartItemOptionProducts()[0],
            $newCartItem->getCartItemOptionProducts()[0]
        );

        $this->assertNotSame(
            $cartItem->getCartItemOptionValues()[0],
            $newCartItem->getCartItemOptionValues()[0]
        );

        $this->assertNotSame(
            $cartItem->getCartItemTextOptionValues()[0],
            $newCartItem->getCartItemTextOptionValues()[0]
        );
    }

    public function testGetOrderItem()
    {
        $cartItem = $this->dummyData->getCartItemFull();
        $orderItem = $cartItem->getOrderItem(new Pricing);

        $this->assertTrue($orderItem instanceof OrderItem);
        $this->assertTrue($orderItem->getProduct() instanceof Product);
        $this->assertSame(2, $orderItem->getQuantity());
        $this->assertTrue($orderItem->getPrice() instanceof Price);
        $this->assertTrue($orderItem->getOrderItemOptionProducts()[0] instanceof OrderItemOptionProduct);
        $this->assertTrue($orderItem->getOrderItemOptionValues()[0] instanceof OrderItemOptionValue);
        $this->assertTrue($orderItem->getOrderItemTextOptionValues()[0] instanceof OrderItemTextOptionValue);
    }

    public function testGetPriceWithOptionProductsAndValuesRetainsCatalogPromotionsAndProductQuantityDiscounts()
    {
        $cartItem = $this->dummyData->getCartItemFull();
        $pricing = $this->dummyData->getPricing();

        $price = $cartItem->getPrice($pricing);

        $this->assertSame(1, count($price->getCatalogPromotions()));
        $this->assertSame(1, count($price->getProductQuantityDiscounts()));
    }
}
