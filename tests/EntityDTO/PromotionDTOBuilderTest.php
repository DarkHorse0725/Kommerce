<?php
namespace inklabs\kommerce\EntityDTO;

use DateTime;
use inklabs\kommerce\tests\Entity\TestablePromotion;
use inklabs\kommerce\tests\Entity\TestablePromotionInvalid;
use RuntimeException;

class PromotionDTOBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $promotion = new TestablePromotion;
        $promotion->setStart(new DateTime('2015-01-29'));
        $promotion->setEnd(new DateTime('2015-01-30'));

        $promotionDTO = $promotion->getDTOBuilder()
            ->build();

        $this->assertTrue($promotionDTO instanceof PromotionDTO);
        $this->assertSame($promotionDTO->startFormatted, '2015-01-29');
        $this->assertSame($promotionDTO->endFormatted, '2015-01-30');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage promotionDTO has not been initialized
     */
    public function testBuildFails()
    {
        $promotion = new TestablePromotionInvalid;
        $promotion->getDTOBuilder();
    }
}
