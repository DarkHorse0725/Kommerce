<?php
namespace inklabs\kommerce\Entity;

use DateTime;
use DateTimeZone;
use Exception;
use inklabs\kommerce\tests\Helper\DoctrineTestCase;

class AbstractPromotionTest extends DoctrineTestCase
{
    /** @var AbstractPromotion */
    protected $promotion;

    /** @var DateTime */
    protected $startDate;

    /** @var DateTime */
    protected $endDate;

    /** @var DateTime */
    protected $validDateInMiddle;

    /** @var DateTime */
    protected $oneYearPrior;

    /** @var DateTime */
    protected $oneYearAfter;

    public function setUp()
    {
        $this->promotion = $this->getMockForAbstractClass(AbstractPromotion::class);

        $this->startDate = new DateTime('2014-01-02 08:00:00', new DateTimeZone('UTC'));
        $this->endDate = new DateTime('2014-12-31 13:00:00', new DateTimeZone('UTC'));

        $this->oneYearPrior = new DateTime('2013-06-01', new DateTimeZone('UTC'));
        $this->validDateInMiddle = new DateTime('2014-06-01', new DateTimeZone('UTC'));
        $this->oneYearAfter = new DateTime('2015-06-01', new DateTimeZone('UTC'));
    }

    public function testCreate()
    {
        $this->promotion->setName('20% Off in 2014');
        $this->promotion->setType(AbstractPromotion::TYPE_PERCENT);
        $this->promotion->setValue(20);
        $this->promotion->setRedemptions(10);
        $this->promotion->setMaxRedemptions(100);
        $this->promotion->setReducesTaxSubtotal(true);
        $this->promotion->setStart(new DateTime);
        $this->promotion->setEnd(new DateTime);

        $this->assertEntityValid($this->promotion);
        $this->assertSame('20% Off in 2014', $this->promotion->getName());
        $this->assertSame(AbstractPromotion::TYPE_PERCENT, $this->promotion->getType());
        $this->assertSame('Percent', $this->promotion->getTypeText());
        $this->assertSame(20, $this->promotion->getValue());
        $this->assertSame(10, $this->promotion->getRedemptions());
        $this->assertSame(100, $this->promotion->getMaxRedemptions());
        $this->assertSame(true, $this->promotion->getReducesTaxSubtotal());
        $this->assertTrue($this->promotion->getStart() instanceof DateTime);
        $this->assertTrue($this->promotion->getEnd() instanceof DateTime);
    }

    private function setDatePromotion()
    {
        $this->promotion->setStart($this->startDate);
        $this->promotion->setEnd($this->endDate);
    }

    public function testIsDateValid()
    {
        $this->setDatePromotion();

        $this->assertFalse($this->promotion->isDateValid($this->getModifiedDate($this->startDate, '-1 second')));
        $this->assertTrue($this->promotion->isDateValid($this->startDate));
        $this->assertTrue($this->promotion->isDateValid($this->getModifiedDate($this->startDate, '+1 second')));

        $this->assertTrue($this->promotion->isDateValid($this->validDateInMiddle));

        $this->assertTrue($this->promotion->isDateValid($this->getModifiedDate($this->endDate, '-1 second')));
        $this->assertTrue($this->promotion->isDateValid($this->endDate));
        $this->assertFalse($this->promotion->isDateValid($this->getModifiedDate($this->endDate, '+1 second')));
    }

    public function testIsDateValidWithNullStartAndEnd()
    {
        $this->setDatePromotion();
        $this->promotion->setStart(null);
        $this->promotion->setEnd(null);

        $this->assertTrue($this->promotion->isDateValid($this->oneYearPrior));
        $this->assertTrue($this->promotion->isDateValid($this->validDateInMiddle));
        $this->assertTrue($this->promotion->isDateValid($this->oneYearAfter));
    }

    public function testIsDateValidWithNullStart()
    {
        $this->setDatePromotion();
        $this->promotion->setStart(null);

        $this->assertTrue($this->promotion->isDateValid($this->oneYearPrior));
        $this->assertTrue($this->promotion->isDateValid($this->validDateInMiddle));
        $this->assertFalse($this->promotion->isDateValid($this->oneYearAfter));
    }

    public function testIsDateValidWithNullEnd()
    {
        $this->setDatePromotion();
        $this->promotion->setEnd(null);

        $this->assertFalse($this->promotion->isDateValid($this->oneYearPrior));
        $this->assertTrue($this->promotion->isDateValid($this->validDateInMiddle));
        $this->assertTrue($this->promotion->isDateValid($this->oneYearAfter));
    }

    public function testIsRedemptionCountValid()
    {
        $this->promotion->setMaxRedemptions(null);
        $this->assertTrue($this->promotion->isRedemptionCountValid());

        $this->promotion->setMaxRedemptions(10);
        $this->promotion->setRedemptions(0);
        $this->assertTrue($this->promotion->isRedemptionCountValid());

        $this->promotion->setRedemptions(9);
        $this->assertTrue($this->promotion->isRedemptionCountValid());

        $this->promotion->setRedemptions(10);
        $this->assertFalse($this->promotion->isRedemptionCountValid());

        $this->promotion->setRedemptions(15);
        $this->assertFalse($this->promotion->isRedemptionCountValid());
    }

    public function testIsValid()
    {
        $this->promotion->setMaxRedemptions(null);
        $this->assertTrue($this->promotion->isValidPromotion(new DateTime));
    }

    public function testGetUnitPriceWithPercent()
    {
        $this->promotion->setType(AbstractPromotion::TYPE_PERCENT);
        $this->promotion->setValue(20);
        $this->assertSame(800, $this->promotion->getUnitPrice(1000));
    }

    public function testGetUnitPriceWithFixed()
    {
        $this->promotion->setType(AbstractPromotion::TYPE_FIXED);
        $this->promotion->setValue(20);
        $this->assertSame(980, $this->promotion->getUnitPrice(1000));
    }

    public function testGetUnitPriceWithExact()
    {
        $this->promotion->setType(AbstractPromotion::TYPE_EXACT);
        $this->promotion->setValue(20);
        $this->assertSame(20, $this->promotion->getUnitPrice(1000));
    }

    public function testGetUnitPriceWithInvalidType()
    {
        $this->promotion->setType(-1);

        $this->setExpectedException(
            Exception::class,
            'Invalid discount type'
        );

        $this->promotion->getUnitPrice(0);
    }

    /**
     * @param DateTime $date
     * @param string $modify
     * @return DateTime
     */
    protected function getModifiedDate(DateTime $date, $modify)
    {
        $modifiedDate = clone $date;
        $modifiedDate->modify($modify);
        return $modifiedDate;
    }
}
