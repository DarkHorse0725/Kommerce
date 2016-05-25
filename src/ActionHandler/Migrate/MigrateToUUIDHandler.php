<?php
namespace inklabs\kommerce\ActionHandler\Migrate;

use Doctrine\ORM\EntityManagerInterface;
use inklabs\kommerce\Entity\CatalogPromotion;
use inklabs\kommerce\Entity\Order;
use inklabs\kommerce\Entity\Tag;
use inklabs\kommerce\Entity\TempUuidTrait;
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
        $this->migrateUsers();
        $this->migrateCatalogPromotions();
        $this->migrateOrders();
    }

    private function migrateAllEntities()
    {
        $this->migrateEntities([
            Tag::class,
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

    private function migrateUsers()
    {
        $entityQuery = $this->getEntityQuery(User::class);
        $this->setUUIDAndFlush($entityQuery);

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
