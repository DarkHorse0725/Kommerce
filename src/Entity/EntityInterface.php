<?php
namespace inklabs\kommerce\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;

interface EntityInterface
{
    public static function loadValidatorMetadata(ClassMetadata $metadata);
}
