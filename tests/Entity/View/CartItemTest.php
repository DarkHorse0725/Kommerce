<?php
namespace inklabs\kommerce\Entity\View;

use inklabs\kommerce\Entity as Entity;
use inklabs\kommerce\Service as Service;

class CartItemTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $entityCartItem = new Entity\CartItem(new Entity\Product, 1);

        $cartitem = CartItem::factory($entityCartItem)
            ->withAllData(new Service\Pricing)
            ->export();

        $this->assertInstanceOf('inklabs\kommerce\Entity\View\Price', $cartitem->price);
        $this->assertInstanceOf('inklabs\kommerce\Entity\View\Product', $cartitem->product);
    }
}