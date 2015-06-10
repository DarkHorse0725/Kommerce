<?php
namespace inklabs\kommerce\tests\Helper\EntityRepository;

use inklabs\kommerce\EntityRepository\CartPriceRuleInterface;
use inklabs\kommerce\Entity;

class FakeCartPriceRule extends AbstractFake implements CartPriceRuleInterface
{
    public function __construct()
    {
        $this->setReturnValue(new Entity\CartPriceRule);
    }

    public function save(Entity\CartPriceRule & $cartPriceRule)
    {
    }

    public function create(Entity\CartPriceRule & $cartPriceRule)
    {
    }

    public function remove(Entity\CartPriceRule & $cartPriceRule)
    {
    }

    public function find($id)
    {
        return $this->getReturnValue();
    }

    public function findAll()
    {
        return $this->getReturnValueAsArray();
    }
}