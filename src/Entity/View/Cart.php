<?php
namespace inklabs\kommerce\Entity\View;

use inklabs\kommerce\Entity as Entity;
use inklabs\kommerce\Entity\Shipping as Shipping;
use inklabs\kommerce\Lib as Lib;
use inklabs\kommerce\Service\Pricing;

class Cart
{
    public $totalItems;
    public $totalQuantity;
    public $shippingWeight;

    /* @var CartTotal */
    public $cartTotal;

    /* @var CartItem[] */
    public $items = [];

    /* @var Coupon[] */
    public $coupons = [];

    public function __construct(Entity\Cart $cart)
    {
        $this->cart = $cart;

        $this->totalItems     = $cart->totalItems();
        $this->totalQuantity  = $cart->totalQuantity();
        $this->shippingWeight = $cart->getShippingWeight();

        return $this;
    }

    public static function factory(Entity\Cart $cart)
    {
        return new static($cart);
    }

    public function export()
    {
        unset($this->cart);
        return $this;
    }

    public function withCartTotal(Pricing $pricing, Shipping\Rate $shippingRate = null, Entity\TaxRate $taxRate = null)
    {
        $this->cartTotal = $this->cart->getTotal($pricing, $shippingRate, $taxRate);

        return $this;
    }

    public function withCartItems(Pricing $pricing)
    {
        foreach ($this->cart->getItems() as $cartItem) {
            $this->items[$cartItem->getId()] = CartItem::factory($cartItem)
                ->withAllData($pricing)
                ->export();
        }

        return $this;
    }

    public function withCoupons()
    {
        foreach ($this->cart->getCoupons() as $coupon) {
            $this->coupons[$coupon->getId()] = Coupon::factory($coupon)
                ->export();
        }

        return $this;
    }


    public function withAllData(Pricing $pricing, Shipping\Rate $shippingRate = null, Entity\TaxRate $taxRate = null)
    {
        return $this
            ->withCartTotal($pricing, $shippingRate, $taxRate)
            ->withCartItems($pricing)
            ->withCoupons();
    }
}
