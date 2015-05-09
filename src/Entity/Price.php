<?php
namespace inklabs\kommerce\Entity;

use inklabs\kommerce\View;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Price implements EntityInterface
{
    public $origUnitPrice;
    public $unitPrice;
    public $origQuantityPrice;
    public $quantityPrice;

    /** @var CatalogPromotion[] */
    protected $catalogPromotions = [];

    /** @var ProductQuantityDiscount[] */
    protected $productQuantityDiscounts = [];

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('origUnitPrice', new Assert\NotNull);
        $metadata->addPropertyConstraint('origUnitPrice', new Assert\Range([
            'min' => -2147483648,
            'max' => 2147483647,
        ]));
        $metadata->addPropertyConstraint('unitPrice', new Assert\NotNull);
        $metadata->addPropertyConstraint('unitPrice', new Assert\Range([
            'min' => -2147483648,
            'max' => 2147483647,
        ]));
        $metadata->addPropertyConstraint('origQuantityPrice', new Assert\NotNull);
        $metadata->addPropertyConstraint('origQuantityPrice', new Assert\Range([
            'min' => -2147483648,
            'max' => 2147483647,
        ]));
        $metadata->addPropertyConstraint('quantityPrice', new Assert\NotNull);
        $metadata->addPropertyConstraint('quantityPrice', new Assert\Range([
            'min' => -2147483648,
            'max' => 2147483647,
        ]));
    }

    public function addCatalogPromotion(CatalogPromotion $catalogPromotion)
    {
        $this->catalogPromotions[] = $catalogPromotion;
    }

    public function getCatalogPromotions()
    {
        return $this->catalogPromotions;
    }

    public function addProductQuantityDiscount(ProductQuantityDiscount $productQuantityDiscount)
    {
        $this->productQuantityDiscounts[] = $productQuantityDiscount;
    }

    public function getProductQuantityDiscounts()
    {
        return $this->productQuantityDiscounts;
    }

    public static function add(Price $a, Price $b)
    {
        $price = new Price;
        $price->unitPrice         = $a->unitPrice         + $b->unitPrice;
        $price->origUnitPrice     = $a->origUnitPrice     + $b->origUnitPrice;
        $price->quantityPrice     = $a->quantityPrice     + $b->quantityPrice;
        $price->origQuantityPrice = $a->origQuantityPrice + $b->origQuantityPrice;

        return $price;
    }

    public function getView()
    {
        return new View\Price($this);
    }
}
