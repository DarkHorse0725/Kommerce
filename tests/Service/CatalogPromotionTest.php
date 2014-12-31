<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\Entity as Entity;
use inklabs\kommerce\tests\Helper as Helper;

class CatalogPromotionTest extends Helper\DoctrineTestCase
{
    public function testFindAll()
    {
        $this->entityManager->persist($this->getCatalogPromotion(1));
        $this->entityManager->persist($this->getCatalogPromotion(2));
        $this->entityManager->flush();
        $this->entityManager->clear();

        $catalogPromotionService = new CatalogPromotion($this->entityManager);
        $catalogPromotions = $catalogPromotionService->findAll();

        $this->assertSame(2, count($catalogPromotions));
    }

    private function getCatalogPromotion($num)
    {
        $catalogPromotion = new Entity\CatalogPromotion;
        $catalogPromotion->setCode('TST' . $num);
        $catalogPromotion->setName('test' . $num);
        $catalogPromotion->setType(Entity\Promotion::TYPE_PERCENT);
        $catalogPromotion->setValue(10);
        $catalogPromotion->setRedemptions(0);
        $catalogPromotion->setStart(new \DateTime('2014-01-01', new \DateTimeZone('UTC')));
        $catalogPromotion->setEnd(new \DateTime('2014-12-31', new \DateTimeZone('UTC')));
        return $catalogPromotion;
    }
}
