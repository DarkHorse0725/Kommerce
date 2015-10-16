<?php
namespace inklabs\kommerce\EntityRepository;

use inklabs\kommerce\Entity\Image;
use inklabs\kommerce\tests\Helper;

class ImageRepositoryTest extends Helper\DoctrineTestCase
{
    protected $metaDataClassNames = [
        'kommerce:Image',
        'kommerce:Tag',
        'kommerce:Product',
    ];

    /** @var ImageRepositoryInterface */
    protected $imageRepository;

    public function setUp()
    {
        $this->imageRepository = $this->getRepositoryFactory()->getImageRepository();
    }

    public function setupImageWithProductAndTag()
    {
        $product = $this->dummyData->getProduct();
        $tag = $this->dummyData->getTag();

        $image = $this->dummyData->getImage();
        $image->setProduct($product);
        $image->setTag($tag);

        $this->entityManager->persist($product);
        $this->entityManager->persist($tag);

        $this->imageRepository->create($image);

        $this->entityManager->flush();
        $this->entityManager->clear();

        return $image;
    }

    public function testCRUD()
    {
        $image = $this->dummyData->getImage();

        $this->imageRepository->create($image);
        $this->assertSame(1, $image->getId());

        $image->setPath('New/Path');
        $this->assertSame(null, $image->getUpdated());

        $this->imageRepository->update($image);
        $this->assertTrue($image->getUpdated() instanceof \DateTime);

        $this->imageRepository->delete($image);
        $this->assertSame(null, $image->getId());
    }

    public function testFindOneById()
    {
        $this->setupImageWithProductAndTag();

        $this->setCountLogger();

        $image = $this->imageRepository->findOneById(1);

        $image->getProduct()->getName();
        $image->getTag()->getName();

        $this->assertTrue($image instanceof Image);
        $this->assertSame(1, $this->getTotalQueries());
    }

    /**
     * @expectedException \inklabs\kommerce\EntityRepository\EntityNotFoundException
     * @expectedExceptionMessage Image not found
     */
    public function testFindOneByIdThrowsException()
    {
        $this->imageRepository->findOneById(1);
    }
}
