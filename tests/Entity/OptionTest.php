<?php
use inklabs\kommerce\Entity\Option;
use inklabs\kommerce\Entity\Product;
use inklabs\kommerce\Entity\VirtualProduct;

class OptionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Option::__construct
	 */
	public function test_construct()
	{
		$current_date = new \DateTime('now', new \DateTimeZone('UTC'));

		$option = new Option;
		$option->name = 'Size';
		$option->type = 'radio';
		$option->description = 'Shirt Size';
		$option->created = new \DateTime('now', new \DateTimeZone('UTC'));

		$this->assertEquals('Size', $option->name);
	}

	public function test_with_products()
	{
		$option = new Option;
		$option->name = 'Size';
		$option->type = 'radio';
		$option->description = 'Navy T-shirt size';

		$product_small = new Product;
		$product_small->sku = 'TS-NAVY-SM';
		$product_small->name = 'Navy T-shirt (small)';
		$product_small->price = 900;

		$product_medium = new Product;
		$product_medium->sku = 'TS-NAVY-MD';
		$product_medium->name = 'Navy T-shirt (medium)';
		$product_medium->price = 1200;

		$product_large = new Product;
		$product_large->sku = 'TS-NAVY-LG';
		$product_large->name = 'Navy T-shirt (large)';
		$product_large->price = 1600;

		$option->add_product($product_small);
		$option->add_product($product_medium);
		$option->add_product($product_large);

		$this->assertEquals('Size', $option->name);
	}

	public function test_with_virtual_products()
	{
		$current_date = new \DateTime('now', new \DateTimeZone('UTC'));

		$option = new Option;
		$option->name = 'Size';
		$option->type = 'radio';
		$option->description = 'Generic Size';

		$virtual_product_small = new VirtualProduct;
		$virtual_product_small->sku = 'SM';
		$virtual_product_small->name = 'Small';

		$virtual_product_medium = new VirtualProduct;
		$virtual_product_medium->sku = 'MD';
		$virtual_product_medium->name = 'Medium';

		$virtual_product_large = new VirtualProduct;
		$virtual_product_large->sku = 'LG';
		$virtual_product_large->name = 'Large';

		$option->add_product($virtual_product_small);
		$option->add_product($virtual_product_medium);
		$option->add_product($virtual_product_large);

		$this->assertEquals('Size', $option->name);
	}
}
