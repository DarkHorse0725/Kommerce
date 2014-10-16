<?php
namespace inklabs\kommerce\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="tag")
 **/
class Tag
{
    use Accessor\Time;
    use OptionSelector;

    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string") **/
    protected $name;

    /** @Column(type="string") **/
    protected $description;

    /** @Column(type="string", name="default_image") **/
    protected $defaultImage;

    /** @Column(type="boolean", name="is_product_group") **/
    protected $isProductGroup;

    /** @Column(type="integer", name="sort_order") **/
    protected $sortOrder;

    /** @Column(type="boolean", name="visible") **/
    protected $isVisible;

    /**
     * @ManyToMany(targetEntity="Product")
     * @JoinTable(name="product_tag")
    **/
    protected $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDefaultImage($defaultImage)
    {
        $this->defaultImage = $defaultImage;
    }

    public function getDefaultImage()
    {
        return $this->defaultImage;
    }

    public function getIsProductGroup()
    {
        return $this->isProductGroup;
    }

    public function setIsProductGroup($isProductGroup)
    {
        $this->isProductGroup = $isProductGroup;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    public function getIsVisible()
    {
        return $this->isVisible;
    }
}
