<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\Lib\CartCalculatorInterface;
use inklabs\kommerce\EntityRepository\RepositoryFactory;
use inklabs\kommerce\Lib\Event\EventDispatcherInterface;
use inklabs\kommerce\Lib\FileManagerInterface;
use inklabs\kommerce\Lib\PaymentGateway\PaymentGatewayInterface;
use inklabs\kommerce\Lib\ShipmentGateway\ShipmentGatewayInterface;

class ServiceFactory
{
    /** @var CartCalculatorInterface */
    private $cartCalculator;

    /** @var RepositoryFactory */
    private $repositoryFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var PaymentGatewayInterface */
    private $paymentGateway;

    /** @var ShipmentGatewayInterface */
    private $shipmentGateway;

    /** @var FileManagerInterface */
    private $fileManager;

    public function __construct(
        RepositoryFactory $repositoryFactory,
        CartCalculatorInterface $cartCalculator,
        EventDispatcherInterface $eventDispatcher,
        PaymentGatewayInterface $paymentGateway,
        ShipmentGatewayInterface $shipmentGateway,
        FileManagerInterface $fileManager
    ) {
        $this->repositoryFactory = $repositoryFactory;
        $this->cartCalculator = $cartCalculator;
        $this->eventDispatcher = $eventDispatcher;
        $this->paymentGateway = $paymentGateway;
        $this->shipmentGateway = $shipmentGateway;
        $this->fileManager = $fileManager;
    }

    /**
     * @return AttachmentService
     */
    public function getAttachmentService()
    {
        return new AttachmentService(
            $this->repositoryFactory->getAttachmentRepository(),
            $this->getFileManager(),
            $this->repositoryFactory->getOrderItemRepository(),
            $this->repositoryFactory->getProductRepository(),
            $this->repositoryFactory->getUserRepository()
        );
    }

    /**
     * @return CartService
     */
    public function getCart()
    {
        return new CartService(
            $this->repositoryFactory->getCartRepository(),
            $this->repositoryFactory->getCouponRepository(),
            $this->eventDispatcher,
            $this->repositoryFactory->getOptionProductRepository(),
            $this->repositoryFactory->getOptionValueRepository(),
            $this->repositoryFactory->getOrderRepository(),
            $this->repositoryFactory->getProductRepository(),
            $this->shipmentGateway,
            $this->repositoryFactory->getTaxRateRepository(),
            $this->repositoryFactory->getTextOptionRepository(),
            $this->repositoryFactory->getUserRepository(),
            $this->getInventoryService()
        );
    }

    public function getCartCalculator()
    {
        return $this->cartCalculator;
    }

    /**
     * @return FileManagerInterface
     */
    public function getFileManager()
    {
        return $this->fileManager;
    }

    /**
     * @return ImageService
     */
    public function getImageService()
    {
        return new ImageService(
            $this->getFileManager(),
            $this->repositoryFactory->getImageRepository(),
            $this->repositoryFactory->getProductRepository(),
            $this->repositoryFactory->getTagRepository()
        );
    }

    /**
     * @return Import\ImportOrderService
     */
    public function getImportOrder()
    {
        return new Import\ImportOrderService(
            $this->repositoryFactory->getOrderRepository(),
            $this->repositoryFactory->getUserRepository()
        );
    }

    /**
     * @return Import\ImportOrderItemService
     */
    public function getImportOrderItem()
    {
        return new Import\ImportOrderItemService(
            $this->repositoryFactory->getOrderRepository(),
            $this->repositoryFactory->getOrderItemRepository(),
            $this->repositoryFactory->getProductRepository()
        );
    }

    /**
     * @return Import\ImportPaymentService
     */
    public function getImportPayment()
    {
        return new Import\ImportPaymentService(
            $this->repositoryFactory->getOrderRepository(),
            $this->repositoryFactory->getPaymentRepository()
        );
    }

    /**
     * @return Import\ImportUserService
     */
    public function getImportUser()
    {
        return new Import\ImportUserService($this->repositoryFactory->getUserRepository());
    }

    public function getInventoryService()
    {
        return new InventoryService(
            $this->repositoryFactory->getInventoryLocationRepository(),
            $this->repositoryFactory->getInventoryTransactionRepository()
        );
    }

    /**
     * @return OrderService
     */
    public function getOrder()
    {
        return new OrderService(
            $this->eventDispatcher,
            $this->getInventoryService(),
            $this->repositoryFactory->getOrderWithHashSegmentGenerator(),
            $this->repositoryFactory->getOrderItemRepository(),
            $this->paymentGateway,
            $this->repositoryFactory->getProductRepository(),
            $this->shipmentGateway
        );
    }

    /**
     * @return ShipmentGatewayInterface
     */
    public function getShipmentGateway()
    {
        return $this->shipmentGateway;
    }

    /**
     * @return UserService
     */
    public function getUser()
    {
        return new UserService(
            $this->repositoryFactory->getUserRepository(),
            $this->repositoryFactory->getUserLoginRepository(),
            $this->repositoryFactory->getUserTokenRepository(),
            $this->eventDispatcher
        );
    }
}
