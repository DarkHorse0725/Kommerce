<?php
use inklabs\kommerce\Pricing;
use inklabs\kommerce\Entity\Cart;
use inklabs\kommerce\Entity\CartTotal;
use inklabs\kommerce\Entity\Coupon;
use inklabs\kommerce\Entity\CatalogPromotion;
use inklabs\kommerce\Entity\Product;
use inklabs\kommerce\Entity\TaxRate;
use inklabs\kommerce\Entity\Shipping;

class CartTest extends PHPUnit_Framework_TestCase
{
	private function setup_product()
	{
		$product = new Product;
		$product->sku = 'TST101';
		$product->name = 'Test Product';

		return $product;
	}

	/**
	 * @covers Cart::add_item
	 * @covers Cart::total_items
	 * @covers Cart::total_quantity
	 */
	public function test_add_item()
	{
		$product = $this->setup_product();
		$product->name = 'Test 1';

		$product2 = $this->setup_product();
		$product2->name = 'Test 2';

		$cart = new Cart;
		$cart->add_item($product, 5);
		$cart->add_item($product2, 5);

		$this->assertEquals(2, $cart->total_items());
		$this->assertEquals(10, $cart->total_quantity());
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_basic()
	{
		$pricing = new Pricing;

		$product = $this->setup_product();
		$product->name = 'Test 1';
		$product->price = 500;

		$product2 = $this->setup_product();
		$product2->name = 'Test 2';
		$product2->price = 300;

		$cart = new Cart;
		$cart->add_item($product, 2);
		$cart->add_item($product2, 1);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 1300;
		$cart_total->subtotal = 1300;
		$cart_total->shipping = 0;
		$cart_total->discount = 0;
		$cart_total->tax = 0;
		$cart_total->total = 1300;
		$cart_total->savings = 0;

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::add_coupon
	 * @covers Cart::get_total
	 */
	public function test_get_total_coupon()
	{
		$pricing = new Pricing(new \DateTime('2014-02-01', new DateTimeZone('UTC')));

		$product = $this->setup_product();
		$product->price = 500;

		$coupon = new Coupon;
		$coupon->name = '20% Off';
		$coupon->discount_type = 'percent';
		$coupon->discount_value = 20;
		$coupon->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$coupon->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$cart = new Cart;
		$cart->add_coupon($coupon);
		$cart->add_item($product, 5);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 2500;
		$cart_total->subtotal = 2500;
		$cart_total->shipping = 0;
		$cart_total->discount = 500;
		$cart_total->tax = 0;
		$cart_total->total = 2000;
		$cart_total->savings = 500;
		$cart_total->coupons = [$coupon];

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_coupon_with_catalog_promotion()
	{
		$catalog_promotion = new CatalogPromotion;
		$catalog_promotion->name = '20% Off';
		$catalog_promotion->discount_type = 'percent';
		$catalog_promotion->discount_value = 20;
		$catalog_promotion->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$catalog_promotion->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$pricing = new Pricing(new \DateTime('2014-02-01', new DateTimeZone('UTC')));
		$pricing->add_catalog_promotion($catalog_promotion);

		$product = $this->setup_product();
		$product->price = 500;

		$coupon = new Coupon;
		$coupon->name = '20% Off';
		$coupon->discount_type = 'percent';
		$coupon->discount_value = 20;
		$coupon->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$coupon->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$cart = new Cart;
		$cart->add_coupon($coupon);
		$cart->add_item($product, 5);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 2500;
		$cart_total->subtotal = 2000;
		$cart_total->shipping = 0;
		$cart_total->discount = 400;
		$cart_total->tax = 0;
		$cart_total->total = 1600;
		$cart_total->savings = 900;
		$cart_total->coupons = [$coupon];

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_coupon_under_min_order_value()
	{
		$pricing = new Pricing(new \DateTime('2014-02-01', new DateTimeZone('UTC')));

		$product = $this->setup_product();
		$product->price = 2000; // $20

		$coupon = new Coupon;
		$coupon->name = '20% Off orders over $100';
		$coupon->discount_type = 'percent';
		$coupon->discount_value = 20;
		$coupon->min_order_value = 10000; // $100
		$coupon->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$coupon->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$cart = new Cart;
		$cart->add_coupon($coupon);
		$cart->add_item($product, 1);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 2000;
		$cart_total->subtotal = 2000;
		$cart_total->shipping = 0;
		$cart_total->discount = 0;
		$cart_total->tax = 0;
		$cart_total->total = 2000;
		$cart_total->savings = 0;
		$cart_total->coupons = [];

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_coupon_over_min_order_value()
	{
		$pricing = new Pricing(new \DateTime('2014-02-01', new DateTimeZone('UTC')));

		$product = $this->setup_product();
		$product->price = 2000; // $20

		$coupon = new Coupon;
		$coupon->name = '20% Off orders over $100';
		$coupon->discount_type = 'percent';
		$coupon->discount_value = 20;
		$coupon->min_order_value = 10000; // $100
		$coupon->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$coupon->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$cart = new Cart;
		$cart->add_coupon($coupon);
		$cart->add_item($product, 6);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 12000;
		$cart_total->subtotal = 12000;
		$cart_total->shipping = 0;
		$cart_total->discount = 2400;
		$cart_total->tax = 0;
		$cart_total->total = 9600;
		$cart_total->savings = 2400;
		$cart_total->coupons = [$coupon];

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_coupon_over_max_order_value()
	{
		$pricing = new Pricing(new \DateTime('2014-02-01', new DateTimeZone('UTC')));

		$product = $this->setup_product();
		$product->price = 2000; // $20

		$coupon = new Coupon;
		$coupon->name = '20% Off orders under $100';
		$coupon->discount_type = 'percent';
		$coupon->discount_value = 20;
		$coupon->max_order_value = 10000; // $100
		$coupon->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$coupon->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$cart = new Cart;
		$cart->add_coupon($coupon);
		$cart->add_item($product, 6);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 12000;
		$cart_total->subtotal = 12000;
		$cart_total->shipping = 0;
		$cart_total->discount = 0;
		$cart_total->tax = 0;
		$cart_total->total = 12000;
		$cart_total->savings = 0;
		$cart_total->coupons = [];

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_coupon_under_max_order_value()
	{
		$pricing = new Pricing(new \DateTime('2014-02-01', new DateTimeZone('UTC')));

		$product = $this->setup_product();
		$product->price = 2000; // $20

		$coupon = new Coupon;
		$coupon->name = '20% Off orders under $100';
		$coupon->discount_type = 'percent';
		$coupon->discount_value = 20;
		$coupon->max_order_value = 10000; // $100
		$coupon->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$coupon->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$cart = new Cart;
		$cart->add_coupon($coupon);
		$cart->add_item($product, 4);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 8000;
		$cart_total->subtotal = 8000;
		$cart_total->shipping = 0;
		$cart_total->discount = 1600;
		$cart_total->tax = 0;
		$cart_total->total = 6400;
		$cart_total->savings = 1600;
		$cart_total->coupons = [$coupon];

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_coupon_valid_order_value()
	{
		$pricing = new Pricing(new \DateTime('2014-02-01', new DateTimeZone('UTC')));

		$product = $this->setup_product();
		$product->price = 2000; // $20

		$coupon = new Coupon;
		$coupon->name = '20% Off orders under $100';
		$coupon->discount_type = 'percent';
		$coupon->discount_value = 20;
		$coupon->min_order_value = 1000; // $10
		$coupon->max_order_value = 10000; // $100
		$coupon->start = new \DateTime('2014-01-01', new DateTimeZone('UTC'));
		$coupon->end   = new \DateTime('2014-12-31', new DateTimeZone('UTC'));

		$cart = new Cart;
		$cart->add_coupon($coupon);
		$cart->add_item($product, 1);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 2000;
		$cart_total->subtotal = 2000;
		$cart_total->shipping = 0;
		$cart_total->discount = 400;
		$cart_total->tax = 0;
		$cart_total->total = 1600;
		$cart_total->savings = 400;
		$cart_total->coupons = [$coupon];

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_with_shipping()
	{
		$pricing = new Pricing;

		$product = $this->setup_product();
		$product->price = 500;

		$usps_shipping_rate = new Shipping\Rate;
		$usps_shipping_rate->code = '4';
		$usps_shipping_rate->name = 'Parcel Post';
		$usps_shipping_rate->cost = 1000;

		$cart = new Cart;
		$cart->add_item($product, 3);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 1500;
		$cart_total->subtotal = 1500;
		$cart_total->shipping = 1000;
		$cart_total->discount = 0;
		$cart_total->tax = 0;
		$cart_total->total = 2500;
		$cart_total->savings = 0;
		$cart_total->tax_rate = $tax_rate;

		$this->assertEquals($cart_total, $cart->get_total($pricing, $usps_shipping_rate));
	}

	/**
	 * @covers Cart::set_tax_rate
	 * @covers Cart::get_total
	 */
	public function test_get_total_with_zip5_tax_not_applied_to_shipping()
	{
		$pricing = new Pricing;

		$product = $this->setup_product();
		$product->price = 500;
		$product->is_taxable = TRUE;

		$tax_rate = new TaxRate;
		$tax_rate->zip5 = 92606;
		$tax_rate->rate = 8.0;
		$tax_rate->apply_to_shipping = FALSE;

		$cart = new Cart;
		$cart->set_tax_rate($tax_rate);
		$cart->add_item($product, 2);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 1000;
		$cart_total->subtotal = 1000;
		$cart_total->tax_subtotal = 1000;
		$cart_total->shipping = 0;
		$cart_total->discount = 0;
		$cart_total->tax = 80;
		$cart_total->total = 1080;
		$cart_total->savings = 0;
		$cart_total->tax_rate = $tax_rate;

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}

	/**
	 * @covers Cart::get_total
	 */
	public function test_get_total_with_zip5_tax_applied_to_shipping()
	{
		$pricing = new Pricing;

		$product = $this->setup_product();
		$product->price = 500;
		$product->is_taxable = TRUE;

		$tax_rate = new TaxRate;
		$tax_rate->zip5 = 92606;
		$tax_rate->rate = 8.0;
		$tax_rate->apply_to_shipping = TRUE;

		$usps_shipping_rate = new Shipping\Rate;
		$usps_shipping_rate->code = '4';
		$usps_shipping_rate->name = 'Parcel Post';
		$usps_shipping_rate->cost = 1000;

		$cart = new Cart;
		$cart->set_tax_rate($tax_rate);
		$cart->add_item($product, 2);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 1000;
		$cart_total->subtotal = 1000;
		$cart_total->tax_subtotal = 1000;
		$cart_total->shipping = 1000;
		$cart_total->discount = 0;
		$cart_total->tax = 160;
		$cart_total->total = 2160;
		$cart_total->savings = 0;
		$cart_total->tax_rate = $tax_rate;

		$this->assertEquals($cart_total, $cart->get_total($pricing, $usps_shipping_rate));
	}

	/**
	 * @covers Cart::set_tax_rate
	 * @covers Cart::get_total
	 */
	public function test_get_total_with_zip5_tax_not_taxable()
	{
		$pricing = new Pricing;

		$product = $this->setup_product();
		$product->price = 500;
		$product->is_taxable = FALSE;

		$tax_rate = new TaxRate;
		$tax_rate->zip5 = 92606;
		$tax_rate->rate = 8.0;
		$tax_rate->apply_to_shipping = FALSE;

		$cart = new Cart;
		$cart->set_tax_rate($tax_rate);
		$cart->add_item($product, 2);

		$cart_total = new CartTotal;
		$cart_total->orig_subtotal = 1000;
		$cart_total->subtotal = 1000;
		$cart_total->shipping = 0;
		$cart_total->discount = 0;
		$cart_total->tax = 0;
		$cart_total->total = 1000;
		$cart_total->savings = 0;

		$this->assertEquals($cart_total, $cart->get_total($pricing));
	}
}
