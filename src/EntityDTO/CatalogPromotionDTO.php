<?php
namespace inklabs\kommerce\EntityDTO;

class CatalogPromotionDTO extends AbstractPromotionDTO
{
    public $code;

    /** @var TagDTO */
    public $tag;
}