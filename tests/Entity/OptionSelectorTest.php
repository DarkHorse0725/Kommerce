<?php
namespace inklabs\kommerce;

class OptionSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddOption()
    {
        $option = new Entity\Option;
        $option->setName('Size');
        $option->setType('radio');
        $option->setDescription('Navy T-shirt size');

        $product_small = new Entity\Product;
        $product_small->setSku('TS-NAVY-SM');
        $product_small->setName('Navy T-shirt (small)');

        $option->addProduct($product_small);

        $product = new Entity\Product;
        $product->setSku('TST101');
        $product->setName('Test Product');

        $product->addOption($option);
        $product->addSelectedOptionProduct($product_small);

        $this->assertEquals('TST101', $product->getSku());
        $this->assertEquals(1, count($product->getOptions()));
    }
}
