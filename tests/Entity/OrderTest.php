<?php
namespace inklabs\kommerce\Entity;

use inklabs\kommerce\Lib\CartCalculator;
use inklabs\kommerce\Lib\Pricing;
use inklabs\kommerce\tests\Helper;
use inklabs\kommerce\Lib\PaymentGateway;
use Symfony\Component\Validator\Validation;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $shippingAddress = new OrderAddress;
        $shippingAddress->firstName = 'John';
        $shippingAddress->lastName = 'Doe';
        $shippingAddress->company = 'Acme Co.';
        $shippingAddress->address1 = '123 Any St';
        $shippingAddress->address2 = 'Ste 3';
        $shippingAddress->city = 'Santa Monica';
        $shippingAddress->state = 'CA';
        $shippingAddress->zip5 = '90401';
        $shippingAddress->zip4 = '3274';
        $shippingAddress->phone = '555-123-4567';
        $shippingAddress->email = 'john@example.com';

        $billingAddress = clone $shippingAddress;

        $product = new Product;
        $product->setSku('sku');
        $product->setName('test name');
        $product->setUnitPrice(500);
        $product->setQuantity(10);

        $orderItem = new OrderItem;
        $orderItem->setProduct($product);
        $orderItem->setQuantity(2);

        $order = new Order;
        $order->setId(1);
        $order->setExternalId('CO1102-0016');
        $order->setShippingAddress($shippingAddress);
        $order->setBillingAddress($billingAddress);
        $order->setUser(new User);
        $order->addCoupon(new Coupon);
        $order->addPayment(new CashPayment(100));
        $order->setReferenceNumber('xxx-xxxxxxx-xxxxxxx');
        $order->setShippingRate(new ShippingRate);
        $order->setTaxRate(new TaxRate);
        $order->addOrderItem($orderItem);
        $order->setTotal(new CartTotal);
        $order->addShipment(new Shipment);

        $validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        $this->assertEmpty($validator->validate($order));
        $this->assertSame(1, $order->getReferenceId());
        $this->assertSame('xxx-xxxxxxx-xxxxxxx', $order->getReferenceNumber());
        $this->assertSame(Order::STATUS_PENDING, $order->getStatus());
        $this->assertSame('Pending', $order->getStatusText());
        $this->assertSame('CO1102-0016', $order->getExternalId());
        $this->assertSame(1, $order->totalItems());
        $this->assertSame(2, $order->totalQuantity());
        $this->assertTrue($order->getTotal() instanceof CartTotal);
        $this->assertTrue($order->getShippingAddress() instanceof OrderAddress);
        $this->assertTrue($order->getBillingAddress() instanceof OrderAddress);
        $this->assertTrue($order->getUser() instanceof User);
        $this->assertTrue($order->getCoupons()[0] instanceof Coupon);
        $this->assertTrue($order->getOrderItem(0) instanceof OrderItem);
        $this->assertTrue($order->getOrderItems()[0] instanceof OrderItem);
        $this->assertTrue($order->getPayments()[0] instanceof AbstractPayment);
        $this->assertTrue($order->getShippingRate() instanceof ShippingRate);
        $this->assertTrue($order->getTaxRate() instanceof TaxRate);
        $this->assertTrue($order->getProducts()[0] instanceof Product);
        $this->assertTrue($order->getShipments()[0] instanceof Shipment);
    }

    public function testCreateFromCart()
    {
        $product = new Product;
        $product->setUnitPrice(500);

        $cartItem = new CartItem;
        $cartItem->setProduct($product);

        $cart = new Cart;
        $cart->setUser(new User);
        $cart->addCartItem($cartItem);
        $cart->addCoupon(new Coupon);
        $cart->setShippingRate(new ShippingRate);
        $cart->setTaxRate(new TaxRate);

        $cartCalculator = new CartCalculator(new Pricing);
        $order = Order::fromCart($cart, $cartCalculator);

        $this->assertTrue($order instanceof Order);
    }
}
