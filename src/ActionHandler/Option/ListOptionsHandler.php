<?php
namespace inklabs\kommerce\ActionHandler\Option;

use inklabs\kommerce\Action\Option\ListOptionsQuery;
use inklabs\kommerce\Entity\Pagination;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactoryInterface;
use inklabs\kommerce\EntityRepository\OptionRepositoryInterface;
use inklabs\kommerce\Lib\Authorization\AuthorizationContextInterface;
use inklabs\kommerce\Lib\Query\QueryHandlerInterface;

final class ListOptionsHandler implements QueryHandlerInterface
{
    /** @var ListOptionsQuery */
    private $query;

    /** @var OptionRepositoryInterface */
    private $optionRepository;

    /** @var DTOBuilderFactoryInterface */
    private $dtoBuilderFactory;

    public function __construct(
        ListOptionsQuery $query,
        OptionRepositoryInterface $optionRepository,
        DTOBuilderFactoryInterface $dtoBuilderFactory
    ) {
        $this->query = $query;
        $this->optionRepository = $optionRepository;
        $this->dtoBuilderFactory = $dtoBuilderFactory;
    }

    public function verifyAuthorization(AuthorizationContextInterface $authorizationContext)
    {
        $authorizationContext->verifyIsAdmin();
    }

    public function handle()
    {
        $paginationDTO = $this->query->getRequest()->getPaginationDTO();
        $pagination = new Pagination($paginationDTO->maxResults, $paginationDTO->page);

        $options = $this->optionRepository->getAllOptions(
            $this->query->getRequest()->getQueryString(),
            $pagination
        );

        $this->query->getResponse()->setPaginationDTOBuilder(
            $this->dtoBuilderFactory->getPaginationDTOBuilder($pagination)
        );

        foreach ($options as $option) {
            $this->query->getResponse()->addOptionDTOBuilder(
                $this->dtoBuilderFactory->getOptionDTOBuilder($option)
            );
        }
    }
}
