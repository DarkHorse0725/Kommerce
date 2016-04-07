<?php
namespace inklabs\kommerce\EntityDTO;

use inklabs\kommerce\tests\Helper\DoctrineTestCase;

class InventoryLocationDTOBuilderTest extends DoctrineTestCase
{
    public function testBuild()
    {
        $inventoryTransaction = $this->dummyData->getInventoryLocation();

        $inventoryTransactionDTO = $inventoryTransaction->getDTOBuilder()
            ->withAllData()
            ->build();

        $this->assertTrue($inventoryTransactionDTO instanceof InventoryLocationDTO);
        $this->assertTrue($inventoryTransactionDTO->warehouse instanceof WarehouseDTO);
    }
}
