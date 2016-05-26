<?php
namespace inklabs\kommerce\ActionHandler\Migrate;

use Doctrine\ORM\EntityManagerInterface;
use inklabs\kommerce\Entity\AbstractPayment;
use inklabs\kommerce\Entity\Attribute;
use inklabs\kommerce\Entity\AttributeValue;
use inklabs\kommerce\Entity\CatalogPromotion;
use inklabs\kommerce\Entity\Order;
use inklabs\kommerce\Entity\Product;
use inklabs\kommerce\Entity\ProductAttribute;
use inklabs\kommerce\Entity\ProductQuantityDiscount;
use inklabs\kommerce\Entity\Shipment;
use inklabs\kommerce\Entity\ShipmentComment;
use inklabs\kommerce\Entity\ShipmentItem;
use inklabs\kommerce\Entity\ShipmentTracker;
use inklabs\kommerce\Entity\Tag;
use inklabs\kommerce\Entity\TaxRate;
use inklabs\kommerce\Entity\TempUuidTrait;
use inklabs\kommerce\Entity\TextOption;
use inklabs\kommerce\Entity\User;
use inklabs\kommerce\Entity\UserLogin;
use inklabs\kommerce\Entity\UserRole;
use inklabs\kommerce\Entity\UserToken;
use inklabs\kommerce\Entity\Warehouse;

class MigrateToUUIDHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle()
    {
        $this->migrateAllEntities();
        $this->migrateAttributes();
        $this->migrateProducts();
        $this->migrateShipments();
        $this->migrateUsers();
        $this->migrateCatalogPromotions();
        $this->migrateOrders();
    }

    private function migrateAllEntities()
    {
        $this->migrateEntities([
            AbstractPayment::class,
            Attribute::class,
            AttributeValue::class,
            Product::class,
            ProductAttribute::class,
            ProductQuantityDiscount::class,
            Shipment::class,
            ShipmentComment::class,
            ShipmentItem::class,
            ShipmentTracker::class,
            Tag::class,
            TaxRate::class,
            TextOption::class,
            User::class,
            UserLogin::class,
            UserRole::class,
            UserToken::class,
            Warehouse::class
        ]);
    }

    private function migrateEntities(array $entityClassNames)
    {
        foreach ($entityClassNames as $entityClassName) {
            $entityQuery = $this->getEntityQuery($entityClassName);
            $this->setUUIDAndFlush($entityQuery);
        }
    }

    private function migrateAttributes()
    {
        $entityQuery = $this->getEntityQuery(Attribute::class);

        foreach ($this->iterate($entityQuery) as $attribute) {
            $this->migrateAttributeValues($attribute);
            $this->migrateAttribute2ProductAttributes($attribute);
        }

        $this->entityManager->flush();
    }

    private function migrateAttributeValues(Attribute $attribute)
    {
        foreach ($attribute->getAttributeValues() as $attributeValue) {
            $attributeValue->setAttributeUuid($attribute->getUuid());
            $this->migrateAttributeValue2ProductAttributes($attributeValue);
        }
    }

    private function migrateAttribute2ProductAttributes(Attribute $attribute)
    {
        foreach ($attribute->getProductAttributes() as $productAttribute) {
            $productAttribute->setAttributeUuid($attribute->getUuid());
        }
    }

    private function migrateAttributeValue2ProductAttributes(AttributeValue $attributeValue)
    {
        foreach ($attributeValue->getProductAttributes() as $productAttribute) {
            $productAttribute->setAttributeValueUuid($attributeValue->getUuid());
        }
    }

    private function migrateProducts()
    {
        $entityQuery = $this->getEntityQuery(Product::class);

        foreach ($this->iterate($entityQuery) as $product) {
            $this->migrateProductQuantityDiscounts($product);
            $this->migrateProductAttributes($product);
        }

        $this->entityManager->flush();
    }

    private function migrateProductQuantityDiscounts(Product $product)
    {
        foreach ($product->getProductQuantityDiscounts() as $productQuantityDiscount) {
            $productQuantityDiscount->setProductUuid($product->getUuid());
        }
    }

    private function migrateProductAttributes(Product $product)
    {
        foreach ($product->getProductAttributes() as $productAttribute) {
            $productAttribute->setProductUuid($product->getUuid());
        }
    }

    private function migrateShipments()
    {
        $entityQuery = $this->getEntityQuery(Shipment::class);

        foreach ($this->iterate($entityQuery) as $shipment) {
            $this->migrateShipmentComments($shipment);
            $this->migrateShipmentItems($shipment);
            $this->migrateShipmentTrackers($shipment);
        }

        $this->entityManager->flush();
    }

    private function migrateShipmentComments(Shipment $shipment)
    {
        foreach ($shipment->getShipmentComments() as $shipmentComment) {
            $shipmentComment->setShipmentUuid($shipment->getUuid());
        }
    }

    private function migrateShipmentItems(Shipment $shipment)
    {
        foreach ($shipment->getShipmentItems() as $shipmentItem) {
            $shipmentItem->setShipmentUuid($shipment->getUuid());
        }
    }

    private function migrateShipmentTrackers(Shipment $shipment)
    {
        foreach ($shipment->getShipmentTrackers() as $shipmentTracker) {
            $shipmentTracker->setShipmentUuid($shipment->getUuid());
        }
    }

    private function migrateUsers()
    {
        $entityQuery = $this->getEntityQuery(User::class);

        foreach ($this->iterate($entityQuery) as $user) {
            $this->migrateUserLogins($user);
            $this->migrateUserTokens($user);
        }

        $this->entityManager->flush();
    }

    private function migrateUserLogins(User $user)
    {
        foreach ($user->getUserLogins() as $userLogin) {
            $userLogin->setUserUuid($user->getUuid());

            $userToken = $userLogin->getUserToken();
            if ($userToken !== null) {
                $userLogin->setUserTokenUuid($userToken->getUuid());
            }
        }
    }

    private function migrateUserTokens(User $user)
    {
        foreach ($user->getUserTokens() as $userToken) {
            $userToken->setUserUuid($user->getUuid());
        }
    }

    private function migrateCatalogPromotions()
    {
        $entityQuery = $this->getEntityQuery(CatalogPromotion::class);
        $this->setUUIDAndFlush($entityQuery);

        foreach ($this->iterate($entityQuery) as $catalogPromotion) {
            /** @var CatalogPromotion $catalogPromotion */
            $tag = $catalogPromotion->getTag();

            if ($tag !== null) {
                $catalogPromotion->setTagUuid($tag->getUuid());
            }
        }
        $this->entityManager->flush();
    }

    private function migrateOrders()
    {
        $entityQuery = $this->getEntityQuery(Order::class);
        $this->setUUIDAndFlush($entityQuery);

        foreach ($this->iterate($entityQuery) as $order) {
            $this->migrateOrderItems($order);
            $this->migratePayments($order);
        }

        $this->entityManager->flush();
    }

    private function migrateOrderItems(Order $order)
    {
        foreach ($order->getOrderItems() as $orderItem) {
            $orderItem->setUuid();
            $orderItem->setOrderUuid($order->getUuid());
        }
    }

    private function migratePayments(Order $order)
    {
        foreach ($order->getPayments() as $payment) {
            $payment->setOrderUuid($order->getUuid());
        }
    }

    /**
     * @param $entityClass
     * @return \Doctrine\ORM\Query
     */
    private function getEntityQuery($entityClass)
    {
        return $this->entityManager->createQueryBuilder()
            ->select('table')
            ->from($entityClass, 'table')
            ->getQuery();
    }

    /**
     * @param $entityQuery
     */
    private function setUUIDAndFlush(\Doctrine\ORM\Query $entityQuery)
    {
        foreach ($this->iterate($entityQuery) as $entity) {
            $entity->setUuid();
        }

        $this->entityManager->flush();
    }

    /**
     * @param \Doctrine\ORM\Query $entityQuery
     * @return \Generator | TempUuidTrait[]
     */
    private function iterate(\Doctrine\ORM\Query $entityQuery)
    {
        foreach ($entityQuery->iterate() as $row) {
            yield $row[0];
        }
    }
}
