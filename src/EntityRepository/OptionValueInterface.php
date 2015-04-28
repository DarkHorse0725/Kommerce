<?php
namespace inklabs\kommerce\EntityRepository;

use inklabs\kommerce\Entity;

interface OptionValueInterface
{
    /**
     * @param int $id
     * @return OptionValue
     */
    public function find($id);

    /**
     * @param $optionValueIds
     * @param Entity\Pagination $pagination
     * @return OptionValue[]
     */
    public function getAllOptionValuesByIds($optionValueIds, Entity\Pagination &$pagination = null);
}
