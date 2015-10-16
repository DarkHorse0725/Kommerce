<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\Entity\Attribute;
use inklabs\kommerce\EntityRepository\EntityNotFoundException;
use inklabs\kommerce\tests\Helper;
use inklabs\kommerce\tests\Helper\EntityRepository\FakeAttributeRepository;

class AttributeServiceTest extends Helper\DoctrineTestCase
{
    /** @var FakeAttributeRepository */
    protected $attributeRepository;

    /** @var AttributeService */
    protected $attributeService;

    public function setUp()
    {
        $this->attributeRepository = new FakeAttributeRepository;
        $this->attributeService = new AttributeService($this->attributeRepository);
    }

    public function testCreate()
    {
        $attribute = $this->dummyData->getAttribute();
        $this->attributeService->create($attribute);
        $this->assertTrue($attribute instanceof Attribute);
    }

    public function testEdit()
    {
        $newName = 'New Name';
        $attribute = $this->dummyData->getAttribute();
        $this->assertNotSame($newName, $attribute->getName());

        $attribute->setName($newName);
        $this->attributeService->edit($attribute);
        $this->assertSame($newName, $attribute->getName());
    }

    public function testFind()
    {
        $this->attributeRepository->create(new Attribute);
        $attributeValue = $this->attributeService->findOneById(1);
        $this->assertTrue($attributeValue instanceof Attribute);
    }

    /**
     * @expectedException \inklabs\kommerce\EntityRepository\EntityNotFoundException
     * @expectedExceptionMessage Attribute not found
     */
    public function testFindMissing()
    {
        $this->attributeService->findOneById(1);
    }
}
