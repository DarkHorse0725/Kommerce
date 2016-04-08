<?php
namespace inklabs\kommerce\EntityDTO;

use inklabs\kommerce\tests\Helper\DoctrineTestCase;

class WarehouseDTOBuilderTest extends DoctrineTestCase
{
    public function testBuild()
    {
        $warehouse = $this->dummyData->getWarehouse();

        $warehouseDTO = $warehouse->getDTOBuilder()
            ->build();

        $this->assertTrue($warehouseDTO instanceof WarehouseDTO);
        $this->assertTrue($warehouseDTO->address instanceof AddressDTO);
        $this->assertTrue($warehouseDTO->address->point instanceof PointDTO);
    }
}
