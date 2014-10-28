<?php
namespace inklabs\kommerce\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Pricing
{
    public $date;

    private $catalogPromotions = [];
    private $productQuantityDiscounts = [];
    private $price;

    public function __construct(\DateTime $date = null)
    {
        if ($date === null) {
            $this->date = new \DateTime('now', new \DateTimeZone('UTC'));
        } else {
            $this->date = $date;
        }
    }

    public function setCatalogPromotions(array $catalogPromotions)
    {
        foreach ($catalogPromotions as $catalogPromotion) {
            $this->addCatalogPromotion($catalogPromotion);
        }
    }

    private function addCatalogPromotion(CatalogPromotion $catalogPromotion)
    {
        $this->catalogPromotions[] = $catalogPromotion;
    }

    public function setProductQuantityDiscounts(\Doctrine\Common\Collections\ArrayCollection $productQuantityDiscounts)
    {
        foreach ($productQuantityDiscounts as $productQuantityDiscount) {
            $this->addProductQuantityDiscount($productQuantityDiscount);
        }

        $this->sortProductQuantityDiscounts();
    }

    private function addProductQuantityDiscount(ProductQuantityDiscount $productQuantityDiscount)
    {
        $this->productQuantityDiscounts[] = $productQuantityDiscount;
    }

    public function sortProductQuantityDiscounts()
    {
        usort(
            $this->productQuantityDiscounts,
            create_function('$a, $b', 'return ($a->getQuantity() < $b->getQuantity());')
        );
    }

    public function getPrice(Product $product, $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;

        $this->price = new Price;
        $this->price->origUnitPrice = $this->product->getPrice();
        $this->price->origQuantityPrice = ($this->price->origUnitPrice * $this->quantity);
        $this->price->unitPrice = $this->price->origUnitPrice;

        $this->applyProductQuantityDiscounts();
        $this->applyCatalogPromotions();
        $this->calculateQuantityPrice();
        $this->applyProductOptionPrices();

        return $this->price;
    }

    private function applyProductQuantityDiscounts()
    {
        foreach ($this->productQuantityDiscounts as $productQuantityDiscount) {
            if ($productQuantityDiscount->isValid($this->date, $this->quantity)) {
                $this->price->unitPrice = $productQuantityDiscount->getUnitPrice($this->price->unitPrice);
                $this->price->addProductQuantityDiscount($productQuantityDiscount);
                break;
            }
        }

        // No prices below zero!
        $this->price->unitPrice = max(0, $this->price->unitPrice);
    }

    private function applyCatalogPromotions()
    {
        foreach ($this->catalogPromotions as $catalogPromotion) {
            if ($catalogPromotion->isValid($this->date, $this->product)) {
                $this->price->unitPrice = $catalogPromotion->getUnitPrice($this->price->unitPrice);
                $this->price->addCatalogPromotion($catalogPromotion);
            }
        }

        // No prices below zero!
        $this->price->unitPrice = max(0, $this->price->unitPrice);
    }

    private function calculateQuantityPrice()
    {
        $this->price->quantityPrice = ($this->price->unitPrice * $this->quantity);
    }

    private function applyProductOptionPrices()
    {
        // TODO: code smell...
        foreach ($this->product->getSelectedOptionProducts() as $optionProduct) {
            $subPricing = new Pricing($this->date);
            $optionProductPrice = $subPricing->getPrice($optionProduct, $this->quantity);

            $this->price->unitPrice          += $optionProductPrice->unitPrice;
            $this->price->origUnitPrice      += $optionProductPrice->origUnitPrice;
            $this->price->origQuantityPrice  += $optionProductPrice->origQuantityPrice;
            $this->price->quantityPrice      += $optionProductPrice->quantityPrice;
        }
    }
}