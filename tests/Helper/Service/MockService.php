<?php
namespace inklabs\kommerce\tests\Helper\Service;

use inklabs\kommerce\Lib\ShipmentGateway\ShipmentGatewayInterface;
use inklabs\kommerce\Service\AttachmentServiceInterface;
use inklabs\kommerce\Service\CartServiceInterface;
use inklabs\kommerce\Service\CouponServiceInterface;
use inklabs\kommerce\Lib\FileManagerInterface;
use inklabs\kommerce\Service\ImageServiceInterface;
use inklabs\kommerce\Service\InventoryServiceInterface;
use inklabs\kommerce\Service\OrderServiceInterface;
use inklabs\kommerce\Service\ProductServiceInterface;
use inklabs\kommerce\Service\TagServiceInterface;
use inklabs\kommerce\Service\UserServiceInterface;
use inklabs\kommerce\tests\Helper\Entity\DummyData;
use Mockery;

class MockService
{
    /** @var DummyData */
    protected $dummyData;

    public function __construct(DummyData $dummyData)
    {
        $this->dummyData = $dummyData;
    }

    /**
     * @param string $className
     * @return Mockery\Mock
     */
    protected function getMockeryMock($className)
    {
        return Mockery::mock($className);
    }

    /**
     * @return AttachmentServiceInterface | Mockery\Mock
     */
    public function getAttachmentService()
    {
        $attachmentService = $this->getMockeryMock(AttachmentServiceInterface::class);
        return $attachmentService;
    }

    /**
     * @return CartServiceInterface | Mockery\Mock
     */
    public function getCartService()
    {
        $cartService = $this->getMockeryMock(CartServiceInterface::class);

        return $cartService;
    }

    /**
     * @return CouponServiceInterface | Mockery\Mock
     */
    public function getCouponService()
    {
        $coupon = $this->dummyData->getCoupon();

        $couponService = $this->getMockeryMock(CouponServiceInterface::class);
        $couponService->shouldReceive('findOneById')
            ->andReturn($coupon);

        $couponService->shouldReceive('getAllCoupons')
            ->andReturn([$coupon]);

        return $couponService;
    }

    /**
     * @return FileManagerInterface | Mockery\Mock
     */
    public function getFileManager()
    {
        $service = $this->getMockeryMock(FileManagerInterface::class);
        return $service;
    }

    /**
     * @return InventoryServiceInterface | Mockery\Mock
     */
    public function getInventoryService()
    {
        $inventoryService = $this->getMockeryMock(InventoryServiceInterface::class);

        return $inventoryService;
    }

    /**
     * @return ImageServiceInterface | Mockery\Mock
     */
    public function getImageService()
    {
        $imageService = $this->getMockeryMock(ImageServiceInterface::class);

        return $imageService;
    }

    /**
     * @return OrderServiceInterface | Mockery\Mock
     */
    public function getOrderService()
    {
        $order = $this->dummyData->getOrder();
        $orderItem = $this->dummyData->getOrderItem();

        $service = $this->getMockeryMock(OrderServiceInterface::class);
        $service->shouldReceive('getOrderItemById')
            ->with($orderItem->getId())
            ->andReturn($orderItem);

        $service->shouldReceive('findOneById')
            ->with($order->getId())
            ->andReturn($order);

        return $service;
    }

    /**
     * @return ProductServiceInterface | Mockery\Mock
     */
    public function getProductService()
    {
        $product = $this->dummyData->getProduct();

        $productService = $this->getMockeryMock(ProductServiceInterface::class);
        $productService->shouldReceive('findOneById')
            ->andReturn($product);

        return $productService;
    }

    /**
     * @return ShipmentGatewayInterface | Mockery\Mock
     */
    public function getShipmentGateway()
    {
        $shipmentRate = $this->dummyData->getShipmentRate(225);

        $shipmentGateway = $this->getMockeryMock(ShipmentGatewayInterface::class);
        $shipmentGateway->shouldReceive('getRates')
            ->andReturn([$shipmentRate]);

        return $shipmentGateway;
    }

    /**
     * @return TagServiceInterface | Mockery\Mock
     */
    public function getTagService()
    {
        $tag = $this->dummyData->getTag();

        $tagService = $this->getMockeryMock(TagServiceInterface::class);
        $tagService->shouldReceive('findOneById')
            ->andReturn($tag);

        $tagService->shouldReceive('getAllTags')
            ->andReturn([$tag]);

        return $tagService;
    }

    /**
     * @return UserServiceInterface | Mockery\Mock
     */
    public function getUserService()
    {
        $user = $this->dummyData->getUser();

        $userService = $this->getMockeryMock(UserServiceInterface::class);
        $userService->shouldReceive('loginWithToken')
            ->andReturn($user);

        return $userService;
    }
}
