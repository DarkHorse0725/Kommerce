<?php
namespace inklabs\kommerce\Entity;

use inklabs\kommerce\View;
use Symfony\Component\Validator\Validation;

class TaxRateTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $taxRate = new TaxRate;
        $taxRate->setState(null);
        $taxRate->setZip5('92606');
        $taxRate->setZip5From(null);
        $taxRate->setZip5To(null);
        $taxRate->setRate(8.0);
        $taxRate->setApplyToShipping(false);

        $validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        $this->assertEmpty($validator->validate($taxRate));
        $this->assertSame(null, $taxRate->getState());
        $this->assertSame('92606', $taxRate->getZip5());
        $this->assertSame(null, $taxRate->getZip5From());
        $this->assertSame(null, $taxRate->getZip5To());
        $this->assertSame(8.0, $taxRate->getRate());
        $this->assertSame(false, $taxRate->getApplyToShipping());
        $this->assertTrue($taxRate->getView() instanceof View\TaxRate);
    }

    public function testGetTax()
    {
        $taxRate = new TaxRate;
        $taxRate->setRate(10.0);
        $taxRate->setApplyToShipping(false);
        $this->assertSame(100, $taxRate->getTax(1000, 500));

        $taxRate->setApplyToShipping(true);
        $this->assertSame(150, $taxRate->getTax(1000, 500));
    }
}
