<?php
namespace inklabs\kommerce\Service\Import;

use Exception;
use inklabs\kommerce\Lib\CSVIterator;
use inklabs\kommerce\tests\Helper;

class ImportOrderItemServiceTest extends Helper\DoctrineTestCase
{
    protected $metaDataClassNames = [
        'kommerce:Order',
        'kommerce:OrderItem',
        'kommerce:Product',
        'kommerce:User',
        'kommerce:Cart',
        'kommerce:TaxRate',
    ];

    public function testImport()
    {
        $this->setupProductsForImport();
        $this->setupOrdersForImport();

        $this->setCountLogger();

        $repositoryFactory = $this->getRepositoryFactory();
        $orderItemService = new ImportOrderItemService(
            $repositoryFactory->getOrderRepository(),
            $repositoryFactory->getOrderItemRepository(),
            $repositoryFactory->getProductRepository()
        );

        $iterator = new CSVIterator(__DIR__ . '/ImportOrderItemServiceTest.csv');
        $importResult = $orderItemService->import($iterator);

        $this->assertSame(37, $importResult->getSuccessCount());
        $this->assertSame(0, $importResult->getFailedCount());
        $this->assertSame(111, $this->getTotalQueries());
    }

    public function testImportFail()
    {
        $orderItemRepository = new Helper\EntityRepository\FakeOrderItemRepository;
        $orderItemRepository->setCrudException(new Exception);
        $orderItemService = new ImportOrderItemService(
            new Helper\EntityRepository\FakeOrderRepository,
            $orderItemRepository,
            new Helper\EntityRepository\FakeProductRepository
        );

        $iterator = new CSVIterator(__DIR__ . '/ImportOrderItemServiceTest.csv');
        $importResult = $orderItemService->import($iterator);

        $this->assertSame(0, $importResult->getSuccessCount());
        $this->assertSame(37, $importResult->getFailedCount());
    }

    private function setupOrdersForImport()
    {
        $cartTotal = $this->dummyData->getCartTotal();

        $order1 = $this->dummyData->getOrder($cartTotal);
        $order2 = $this->dummyData->getOrder($cartTotal);
        $order3 = $this->dummyData->getOrder($cartTotal);

        $order1->setExternalId('CO1102-0016');
        $order2->setExternalId('CO1103-0027');
        $order3->setExternalId('CO1104-0032');

        $this->entityManager->persist($order1);
        $this->entityManager->persist($order2);
        $this->entityManager->persist($order3);

        $this->entityManager->flush();
    }

    private function setupProductsForImport()
    {
        $products = [
            $this->dummyData->getProduct('SKU03BAN'),
            $this->dummyData->getProduct('SKU03BOP'),
            $this->dummyData->getProduct('SKU03CCM'),
            $this->dummyData->getProduct('SKU03ODR'),
            $this->dummyData->getProduct('SKU03SPC'),
            $this->dummyData->getProduct('SKU03VAN'),
            $this->dummyData->getProduct('SKU06BAN'),
            $this->dummyData->getProduct('SKU06BLC'),
            $this->dummyData->getProduct('SKU06BOP'),
            $this->dummyData->getProduct('SKU06ODR'),
            $this->dummyData->getProduct('SKU06VAN'),
            $this->dummyData->getProduct('SKU12BAN'),
            $this->dummyData->getProduct('SKU12BOP'),
            $this->dummyData->getProduct('SKU12CCM'),
            $this->dummyData->getProduct('SKU12CNR'),
            $this->dummyData->getProduct('SKU12COL'),
            $this->dummyData->getProduct('SKU12LVS'),
            $this->dummyData->getProduct('SKU12ODR'),
            $this->dummyData->getProduct('SKU12SPC'),
            $this->dummyData->getProduct('SKU12VAN'),
            $this->dummyData->getProduct('SKUFFF'),
            $this->dummyData->getProduct('SKUFFL'),
            $this->dummyData->getProduct('SKUGRN'),
            $this->dummyData->getProduct('SKUSND'),
            $this->dummyData->getProduct('SKUTBR'),
            $this->dummyData->getProduct('SKUTCR'),
        ];

        foreach ($products as $product) {
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }
}
