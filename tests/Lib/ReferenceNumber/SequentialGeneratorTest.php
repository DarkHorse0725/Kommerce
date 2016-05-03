<?php
namespace inklabs\kommerce\Lib\ReferenceNumber;

use inklabs\kommerce\tests\Helper\Entity\FakeReferenceNumberEntity;

class SequentialGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function providerBase()
    {
        return [
            ['0000000000',  null],
            ['0000000000',  0],
            ['0000000001',  1],
            ['2147483647',  2147483647],
            ['4294967295',  4294967295],
            ['9999999999',  9999999999],
            ['99999999999', 99999999999],
        ];
    }

    /**
     * @dataProvider providerBase()
     */
    public function testGenerate($expected, $input)
    {
        $sequentialGenerator = new SequentialGenerator;

        $entity = new FakeReferenceNumberEntity;
        $entity->id = $input;

        $sequentialGenerator->generate($entity);

        $this->assertSame($expected, $entity->getReferenceNumber());
    }

    public function testGenerateWithOffset()
    {
        $sequentialGenerator = new SequentialGenerator;
        $sequentialGenerator->setOffset(1000);

        $entity = new FakeReferenceNumberEntity;
        $entity->id = 1;

        $sequentialGenerator->generate($entity);

        $this->assertSame('0000001001', $entity->getReferenceNumber());
    }

    public function testGenerateWithZeroPadLength()
    {
        $sequentialGenerator = new SequentialGenerator;
        $sequentialGenerator->setOffset(1000);
        $sequentialGenerator->setPadLength(0);

        $entity = new FakeReferenceNumberEntity;
        $entity->id = 1;

        $sequentialGenerator->generate($entity);

        $this->assertSame('1001', $entity->getReferenceNumber());
    }
}
