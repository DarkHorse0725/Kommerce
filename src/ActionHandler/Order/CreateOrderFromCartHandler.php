<?php
namespace inklabs\kommerce\ActionHandler\Order;

use inklabs\kommerce\Action\Order\CreateOrderFromCartCommand;
use inklabs\kommerce\Entity\EntityValidatorException;
use inklabs\kommerce\EntityDTO\Builder\CreditCardDTOBuilder;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactoryInterface;
use inklabs\kommerce\EntityDTO\Builder\OrderAddressDTOBuilder;
use inklabs\kommerce\Lib\CartCalculatorInterface;
use inklabs\kommerce\Service\CartServiceInterface;
use inklabs\kommerce\Service\OrderServiceInterface;

final class CreateOrderFromCartHandler
{
    /** @var CartServiceInterface */
    private $cartService;

    /** @var CartCalculatorInterface */
    private $cartCalculator;

    /** @var OrderServiceInterface */
    private $orderService;

    /** @var DTOBuilderFactoryInterface */
    private $dtoBuilderFactory;

    public function __construct(
        CartServiceInterface $cartService,
        CartCalculatorInterface $cartCalculator,
        OrderServiceInterface $orderService,
        DTOBuilderFactoryInterface $dtoBuilderFactory
    ) {
        $this->cartService = $cartService;
        $this->cartCalculator = $cartCalculator;
        $this->orderService = $orderService;
        $this->dtoBuilderFactory = $dtoBuilderFactory;
    }

    /**
     * @param CreateOrderFromCartCommand $command
     * @throws EntityValidatorException
     */
    public function handle(CreateOrderFromCartCommand $command)
    {
        $cart = $this->cartService->findOneById($command->getCartId());

        $order = $this->orderService->createOrderFromCart(
            $command->getOrderId(),
            $cart,
            $this->cartCalculator,
            $command->getIp4(),
            OrderAddressDTOBuilder::createFromDTO($command->getShippingAddressDTO()),
            OrderAddressDTOBuilder::createFromDTO($command->getBillingAddressDTO()),
            CreditCardDTOBuilder::createFromDTO($command->getCreditCardDTO())
        );

        $this->cartService->delete($cart);
    }
}
