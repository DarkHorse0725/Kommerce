<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\Entity as Entity;

class Cart extends \inklabs\kommerce\Lib\EntityManager
{
    protected $sessionManager;
    protected $cartSessionKey = 'newcart';
    protected $cart;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \inklabs\kommerce\Lib\SessionManager $sessionManager
    ) {
        $this->setEntityManager($entityManager);
        $this->sessionManager = $sessionManager;

        $this->load();
        if (! ($this->cart instanceof Entity\Cart)) {
            $pricing = new Pricing;
            $pricing->loadCatalogPromotions($entityManager);

            $this->cart = new Entity\Cart($pricing);
            $this->save();
        }
    }

    private function load()
    {
        $this->cart = $this->sessionManager->get($this->cartSessionKey);
    }

    private function save()
    {
        $this->sessionManager->set($this->cartSessionKey, $this->cart);
    }

    public function addItem(Entity\Product $product, $quantity)
    {
        $itemId = $this->cart->addItem($product, $quantity);
        $this->save();

        return $itemId;
    }

    public function getItems()
    {
        return $this->cart->getItems();
    }

    public function getItem($id)
    {
        return $this->cart->getItem($id);
    }

    public function totalItems()
    {
        return $this->cart->totalItems();
    }

    public function getTotal()
    {
        return $this->cart->getTotal();
    }
}
