<?php
namespace inklabs\kommerce\Service\Import;

use inklabs\kommerce\Entity;
use inklabs\kommerce\View;
use inklabs\kommerce\Lib;
use inklabs\kommerce\tests\Helper;

class OrderTest extends Helper\DoctrineTestCase
{
    protected $metaDataClassNames = [
        'kommerce:Order',
        'kommerce:User',
        'kommerce:Cart',
    ];

    public function testImport()
    {
        $this->setupUsersForImport();

        $this->setCountLogger();

        $factoryRepository = $this->repository();
        $orderService = new Order(
            $factoryRepository->getOrderRepository(),
            $factoryRepository->getUserRepository()
        );

        $iterator = new Lib\CSVIterator(__DIR__ . '/OrderTest.csv');
        $importResult = $orderService->import($iterator);

        $this->assertSame(3, $importResult->getSuccessCount());
        $this->assertSame(1, $importResult->getFailedCount());
        $this->assertSame(13, $this->countSQLLogger->getTotalQueries());
    }

    private function setupUsersForImport()
    {
        $user19 = $this->getDummyUser(19);
        $user42 = $this->getDummyUser(42);
        $user52 = $this->getDummyUser(52);

        $this->entityManager->persist($user19);
        $this->entityManager->persist($user42);
        $this->entityManager->persist($user52);
        $this->entityManager->flush();
    }
}
