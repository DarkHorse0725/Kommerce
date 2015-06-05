<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\Entity;
use inklabs\kommerce\View;
use inklabs\kommerce\tests\Helper;
use inklabs\kommerce\tests\Helper\EntityRepository\FakeCatalogPromotion;

class CatalogPromotionTest extends Helper\DoctrineTestCase
{
    /** @var FakeCatalogPromotion */
    protected $catalogPromotionRepository;

    /** @var CatalogPromotion */
    protected $catalogPromotionService;

    public function setUp()
    {
        $this->catalogPromotionRepository = new FakeCatalogPromotion;
        $this->catalogPromotionService = new CatalogPromotion($this->catalogPromotionRepository);
    }

    public function testCreate()
    {
        $catalogPromotion = $this->getDummyCatalogPromotion();
        $this->catalogPromotionService->create($catalogPromotion);
        $this->assertTrue($catalogPromotion instanceof Entity\CatalogPromotion);
    }

    public function testEdit()
    {
        $newName = 'New Name';
        $catalogPromotion = $this->getDummyCatalogPromotion();
        $this->assertNotSame($newName, $catalogPromotion->getName());

        $catalogPromotion->setName($newName);
        $this->catalogPromotionService->edit($catalogPromotion);
        $this->assertSame($newName, $catalogPromotion->getName());
    }

    public function testFind()
    {
        $catalogPromotion = $this->catalogPromotionService->find(1);
        $this->assertTrue($catalogPromotion instanceof View\CatalogPromotion);
    }

    public function testFindMissing()
    {
        $this->catalogPromotionRepository->setReturnValue(null);

        $catalogPromotion = $this->catalogPromotionService->find(0);
        $this->assertSame(null, $catalogPromotion);
    }

    public function testFindAll()
    {
        $catalogPromotions = $this->catalogPromotionService->findAll();
        $this->assertTrue($catalogPromotions[0] instanceof View\CatalogPromotion);
    }

    public function testGetAllCatalogPromotions()
    {
        $catalogPromotions = $this->catalogPromotionService->getAllCatalogPromotions();
        $this->assertTrue($catalogPromotions[0] instanceof View\CatalogPromotion);
    }

    public function testAllGetCatalogPromotionsByIds()
    {
        $catalogPromotions = $this->catalogPromotionService->getAllCatalogPromotionsByIds([1]);
        $this->assertTrue($catalogPromotions[0] instanceof View\CatalogPromotion);
    }
}
