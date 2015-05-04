<?php
namespace inklabs\kommerce\View\Payment;

use inklabs\kommerce\Entity;

class Credit extends Payment
{
    public $chargeResponse;

    public function __construct(Entity\Payment\Credit $credit)
    {
        parent::__construct($credit);

        $this->chargeResponse = $credit->getChargeResponse()->getView();

        return $this;
    }
}