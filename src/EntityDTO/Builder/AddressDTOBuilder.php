<?php
namespace inklabs\kommerce\EntityDTO\Builder;

use inklabs\kommerce\Entity\Address;
use inklabs\kommerce\EntityDTO\AddressDTO;

class AddressDTOBuilder
{
    /** @var Address */
    private $address;

    /** @var AddressDTO */
    private $addressDTO;

    public function __construct(Address $address)
    {
        $this->address = $address;

        $this->addressDTO = new AddressDTO;
        $this->addressDTO->attention = $this->address->getAttention();
        $this->addressDTO->company   = $this->address->getCompany();
        $this->addressDTO->address1  = $this->address->getaddress1();
        $this->addressDTO->address2  = $this->address->getaddress2();
        $this->addressDTO->city      = $this->address->getcity();
        $this->addressDTO->state     = $this->address->getstate();
        $this->addressDTO->zip5      = $this->address->getzip5();
        $this->addressDTO->zip4      = $this->address->getzip4();
        $this->addressDTO->point     = $this->address->getPoint()->getDTOBuilder()->build();
    }

    public function build()
    {
        return $this->addressDTO;
    }
}
