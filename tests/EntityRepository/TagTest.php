<?php
namespace inklabs\kommerce\EntityRepository;

use inklabs\kommerce\Entity as Entity;
use inklabs\kommerce\Lib\BaseConvert;
use inklabs\kommerce\tests\Helper as Helper;

class TagTest extends Helper\DoctrineTestCase
{
    /* @var Entity\Tag */
    protected $product;

    /**
     * @return Tag
     */
    private function getRepository()
    {
        return $this->entityManager->getRepository('kommerce:Tag');
    }

    public function setUp()
    {
        $tag = new Entity\Tag;
        $tag->setName('Test Tag');
        $tag->setDescription('Test Description');
        $tag->setDefaultImage('http://lorempixel.com/400/200/');
        $tag->setSortOrder(0);
        $tag->setIsVisible(true);

        $this->entityManager->persist($tag);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * @return Entity\Tag
     */
    private function getDummyTag($num)
    {
        $tag = new Entity\Tag;
        $tag->setName('Test Tag ' . $num);
        $tag->setDescription('Test Description');
        $tag->setDefaultImage('http://lorempixel.com/400/200/');
        $tag->setSortOrder(0);
        $tag->setIsVisible(true);
        return $tag;
    }

    public function testFind()
    {
        $tag = $this->getRepository()
            ->find(1);

        $this->assertSame(1, $tag->getId());
    }

    public function testGetAllTags()
    {
        $tag1 = $this->getDummyTag(1);

        $this->entityManager->persist($tag1);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $tags = $this->getRepository()
            ->getAllTags('Test');

        $this->assertSame(1, $tags[0]->getId());
    }
}
