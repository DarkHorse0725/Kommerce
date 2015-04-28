<?php
namespace inklabs\kommerce\EntityRepository;

use inklabs\kommerce\Entity;
use inklabs\kommerce\tests\Helper;

class ImageTest extends Helper\DoctrineTestCase
{
    protected $metaDataClassNames = [
        'kommerce:Image',
        'kommerce:Tag',
        'kommerce:Product',
    ];

    /** @var ImageInterface */
    protected $imageRepository;

    public function setUp()
    {
        $this->imageRepository = $this->entityManager->getRepository('kommerce:Image');
    }

    public function setupImageWithProductAndTag()
    {
        $product = $this->getDummyProduct();
        $tag = $this->getDummyTag();

        $image = $this->getDummyImage();
        $image->setProduct($product);
        $image->setTag($tag);

        $this->entityManager->persist($image);
        $this->entityManager->persist($product);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function testFind()
    {
        $this->setupImageWithProductAndTag();

        $this->setCountLogger();

        $image = $this->imageRepository->find(1);

        $image->getProduct()->getName();
        $image->getTag()->getName();

        $this->assertTrue($image instanceof Entity\Image);
        $this->assertSame(1, $this->countSQLLogger->getTotalQueries());
    }
}
