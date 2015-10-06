<?php
namespace inklabs\kommerce\Entity;

use inklabs\kommerce\EntityDTO\Builder\ProductDTOBuilder;
use inklabs\kommerce\View;
use Doctrine\Common\Collections\ArrayCollection;
use inklabs\kommerce\Lib;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Product implements ValidationInterface
{
    use TimeTrait, IdTrait;

    /** @var string */
    protected $sku;

    /** @var string */
    protected $name;

    /** @var int */
    protected $unitPrice;

    /** @var int */
    protected $quantity;

    /** @var bool */
    protected $isInventoryRequired;

    /** @var bool */
    protected $isPriceVisible;

    /** @var bool */
    protected $isActive;

    /** @var bool */
    protected $isVisible;

    /** @var bool */
    protected $isTaxable;

    /** @var bool */
    protected $isShippable;

    /** @var int */
    protected $shippingWeight;

    /** @var string */
    protected $description;

    /** @var float */
    protected $rating;

    /** @var string */
    protected $defaultImage;

    /** @var Tag[] */
    protected $tags;

    /** @var Image[] */
    protected $images;

    /** @var ProductQuantityDiscount[] */
    protected $productQuantityDiscounts;

    /** @var OptionProduct[] */
    protected $optionProducts;

    /** @var ProductAttribute[] */
    protected $productAttributes;

    public function __construct()
    {
        $this->setCreated();
        $this->tags = new ArrayCollection;
        $this->images = new ArrayCollection;
        $this->productQuantityDiscounts = new ArrayCollection;
        $this->productAttributes = new ArrayCollection;
        $this->optionProducts = new ArrayCollection;

        $this->isInventoryRequired = false;
        $this->isPriceVisible = false;
        $this->isActive = false;
        $this->isVisible = false;
        $this->isTaxable = false;
        $this->isShippable = false;

        $this->unitPrice = 0;
        $this->quantity = 0;
        $this->shippingWeight = 0;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank);
        $metadata->addPropertyConstraint('name', new Assert\Length([
            'max' => 255,
        ]));

        $metadata->addPropertyConstraint('sku', new Assert\Length([
            'max' => 64,
        ]));

        $metadata->addPropertyConstraint('description', new Assert\Length([
            'max' => 65535,
        ]));

        $metadata->addPropertyConstraint('unitPrice', new Assert\NotNull);
        $metadata->addPropertyConstraint('unitPrice', new Assert\Range([
            'min' => 0,
            'max' => 4294967295,
        ]));

        $metadata->addPropertyConstraint('quantity', new Assert\NotNull);
        $metadata->addPropertyConstraint('quantity', new Assert\Range([
            'min' => 0,
            'max' => 65535,
        ]));

        $metadata->addPropertyConstraint('shippingWeight', new Assert\NotNull);
        $metadata->addPropertyConstraint('shippingWeight', new Assert\Range([
            'min' => 0,
            'max' => 65535,
        ]));
    }

    public function loadFromView(View\Product $viewProduct)
    {
        $this->setName($viewProduct->name);
        $this->setDefaultImage($viewProduct->defaultImage);
        $this->setUnitPrice($viewProduct->unitPrice);
        $this->setQuantity($viewProduct->quantity);
        $this->setIsInventoryRequired($viewProduct->isInventoryRequired);
        $this->setIsPriceVisible($viewProduct->isPriceVisible);
        $this->setIsActive($viewProduct->isActive);
        $this->setIsVisible($viewProduct->isVisible);
        $this->setIsTaxable($viewProduct->isTaxable);
        $this->setIsShippable($viewProduct->isShippable);
        $this->setSku($viewProduct->sku);
        $this->setShippingWeight($viewProduct->shippingWeight);
        $this->setDescription($viewProduct->description);
    }

    public function getPrice(Lib\PricingInterface $pricing, $quantity = 1)
    {
        return $pricing->getPrice(
            $this,
            $quantity
        );
    }

    public function setSku($sku)
    {
        if (trim($sku) === '') {
            $this->sku = null;
        } else {
            $this->sku = (string) $sku;
        }
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setName($name)
    {
        $this->name = (string) $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = (int) $unitPrice;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param boolean $isInventoryRequired
     */
    public function setIsInventoryRequired($isInventoryRequired)
    {
        $this->isInventoryRequired = (bool) $isInventoryRequired;
    }

    public function isInventoryRequired()
    {
        return $this->isInventoryRequired;
    }

    /**
     * @param boolean $isPriceVisible
     */
    public function setIsPriceVisible($isPriceVisible)
    {
        $this->isPriceVisible = (bool) $isPriceVisible;
    }

    public function isPriceVisible()
    {
        return $this->isPriceVisible;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = (bool) $isActive;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = (bool) $isVisible;
    }

    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param boolean $isShippable
     */
    public function setIsShippable($isShippable)
    {
        $this->isShippable = (bool) $isShippable;
    }

    public function isShippable()
    {
        return $this->isShippable;
    }

    public function setShippingWeight($shippingWeight)
    {
        $this->shippingWeight = (int) $shippingWeight;
    }

    public function getShippingWeight()
    {
        return $this->shippingWeight;
    }

    public function setDescription($description)
    {
        if (trim($description) === '') {
            $this->description = null;
        } else {
            $this->description = (string) $description;
        }
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDefaultImage($defaultImage)
    {
        if (trim($defaultImage) === '') {
            $this->defaultImage = null;
        } else {
            $this->defaultImage = (string) $defaultImage;
        }
    }

    public function getDefaultImage()
    {
        return $this->defaultImage;
    }

    public function setIsTaxable($isTaxable)
    {
        $this->isTaxable = (bool) $isTaxable;
    }

    public function isTaxable()
    {
        return $this->isTaxable;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = (float) $rating;
    }

    public function getRating()
    {
        if ($this->rating === null) {
            return null;
        }

        return ($this->rating / 100);
    }

    public function inStock()
    {
        if ($this->isInventoryRequired && ($this->quantity < 1)) {
            return false;
        } else {
            return true;
        }
    }

    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);
    }

    public function removeTag(Tag $tag)
    {
        return $this->tags->removeElement($tag);
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function addImage(Image $image)
    {
        if ($this->images->isEmpty()) {
            $this->setDefaultImage($image->getPath());
        }

        $this->images->add($image);
    }

    public function removeImage(Image $image)
    {
        $result = $this->images->removeElement($image);

        if ($this->getDefaultImage() === $image->getPath()) {
            if (! $this->images->isEmpty()) {
                $this->setDefaultImage($this->images->first()->getPath());
            } else {
                $this->setDefaultImage(null);
            }
        }

        return $result;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function addProductQuantityDiscount(ProductQuantityDiscount $productQuantityDiscount)
    {
        $this->productQuantityDiscounts->add($productQuantityDiscount);
        $productQuantityDiscount->setProduct($this);
    }

    public function removeProductQuantityDiscount(ProductQuantityDiscount $productQuantityDiscount)
    {
        return $this->productQuantityDiscounts->removeElement($productQuantityDiscount);
    }

    public function getProductQuantityDiscounts()
    {
        return $this->productQuantityDiscounts;
    }

    public function getOptionProducts()
    {
        return $this->optionProducts;
    }

    public function addOptionProduct(OptionProduct $optionProduct)
    {
        $this->optionProducts[] = $optionProduct;
    }

    public function getProductAttributes()
    {
        return $this->productAttributes;
    }

    public function addProductAttribute(ProductAttribute $productAttribute)
    {
        $this->productAttributes[] = $productAttribute;
    }

    public function getView()
    {
        return new View\Product($this);
    }

    public function getDTOBuilder()
    {
        return new ProductDTOBuilder($this);
    }
}
