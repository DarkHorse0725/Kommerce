<?php
namespace inklabs\kommerce\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class ShipmentItem implements EntityInterface, ValidationInterface
{
    use IdTrait, TimeTrait;

    /** @var OrderItem */
    private $orderItem;

    /** @var int */
    private $quantityToShip;

    public function __construct(OrderItem $orderItem, $quantityToShip)
    {
        $this->orderItem = $orderItem;
        $this->quantityToShip = (int) $quantityToShip;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('quantityToShip', new Assert\NotNull);
        $metadata->addPropertyConstraint('quantityToShip', new Assert\Range([
            'min' => 0,
            'max' => 65535,
        ]));
    }

    public function getOrderItem()
    {
        return $this->orderItem;
    }

    public function getQuantityToShip()
    {
        return $this->quantityToShip;
    }
}