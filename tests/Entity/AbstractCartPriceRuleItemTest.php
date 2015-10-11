<?php
namespace inklabs\kommerce\Entity;

use Symfony\Component\Validator\Validation;

class AbstractCartPriceRuleItemTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        /** @var AbstractCartPriceRuleItem|\PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockForAbstractClass(AbstractCartPriceRuleItem::class);
        $mock->expects($this->any())
            ->method('matches')
            ->will($this->returnValue(true));

        $mock->setQuantity(2);
        $mock->setCartPriceRule(new CartPriceRule);

        $validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        $this->assertEmpty($validator->validate($mock));
        $this->assertTrue($mock->matches(new CartItem(new Product, 1)));
        $this->assertSame(2, $mock->getQuantity());
        $this->assertTrue($mock->getCartPriceRule() instanceof CartPriceRule);
    }
}
