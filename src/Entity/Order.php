<?php
namespace inklabs\kommerce\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use inklabs\kommerce\Service\Pricing;
use inklabs\kommerce\Entity\Payment as Payment;

class Order
{
    use Accessor\Time;

    protected $id;
    protected $status = 'pending'; // 'pending','processing','shipped','complete','canceled'

    protected $total;
    protected $shippingAddress;
    protected $billingAddress;

    protected $items;
    protected $payments;

    public function __construct(
        Cart $cart,
        Pricing $pricing,
        Shipping\Rate $shippingRate = null,
        TaxRate $taxRate = null
    ) {
        $this->setCreated();
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->total = $cart->getTotal($pricing, $shippingRate, $taxRate);
        $this->setItems($cart->getItems(), $pricing);
    }

    private function setItems($cartItems, Pricing $pricing)
    {
        foreach ($cartItems as $cartItem) {
            $this->addItem($cartItem, $pricing);
        }
    }

    private function addItem(CartItem $cartItem, Pricing $pricing)
    {
        $this->items[] = new OrderItem($cartItem, $pricing);

        end($this->items);
        $id = key($this->items);

        return $id;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setShippingAddress(OrderAddress $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function setBillingAddress(OrderAddress $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addPayment(Payment\Payment $payment)
    {
        $this->payments[] = $payment;
    }

    public function getPayments()
    {
        return $this->payments;
    }
}
