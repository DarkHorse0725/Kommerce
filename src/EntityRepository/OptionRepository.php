<?php
namespace inklabs\kommerce\EntityRepository;

use inklabs\kommerce\Entity\Pagination;

class OptionRepository extends AbstractRepository implements OptionRepositoryInterface
{
    public function getAllOptionsByIds(array $optionIds, Pagination & $pagination = null)
    {
        return $this->getQueryBuilder()
            ->select('Option')
            ->from('kommerce:Option', 'Option')
            ->where('Option.id IN (:optionIds)')
            ->setParameter('optionIds', $optionIds)
            ->paginate($pagination)
            ->getQuery()
            ->getResult();
    }

    public function getAllOptions($queryString, Pagination & $pagination = null)
    {
        $query = $this->getQueryBuilder()
            ->select('option')
            ->from('kommerce:Option', 'option');

        if ($queryString !== null) {
            $query
                ->where('option.name LIKE :query')
                ->orWhere('option.description LIKE :query')
                ->setParameter('query', '%' . $queryString . '%');
        }

        return $query
            ->paginate($pagination)
            ->getQuery()
            ->getResult();
    }
}
