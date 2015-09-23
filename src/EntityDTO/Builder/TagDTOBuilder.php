<?php
namespace inklabs\kommerce\EntityDTO\Builder;

use inklabs\kommerce\Entity\Tag;
use inklabs\kommerce\EntityDTO\TagDTO;
use inklabs\kommerce\Lib\BaseConvert;
use inklabs\kommerce\Lib\Slug;

class TagDTOBuilder
{
    /** @var Tag */
    protected $tag;

    /** @var TagDTO */
    protected $tagDTO;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;

        $this->tagDTO = new TagDTO;
        $this->tagDTO->id           = $this->tag->getId();
        $this->tagDTO->encodedId    = BaseConvert::encode($this->tag->getId());
        $this->tagDTO->slug         = Slug::get($this->tag->getName());
        $this->tagDTO->name         = $this->tag->getName();
        $this->tagDTO->code         = $this->tag->getCode();
        $this->tagDTO->description  = $this->tag->getDescription();
        $this->tagDTO->defaultImage = $this->tag->getDefaultImage();
        $this->tagDTO->sortOrder    = $this->tag->getSortOrder();
        $this->tagDTO->isVisible    = $this->tag->isVisible();
        $this->tagDTO->isActive     = $this->tag->isActive();
        $this->tagDTO->created      = $this->tag->getCreated();
        $this->tagDTO->updated      = $this->tag->getUpdated();
    }

    public function build()
    {
        return $this->tagDTO;
    }
}
