<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\Entity\ValidationInterface;
use inklabs\kommerce\Lib;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Exception\ValidatorException;

abstract class AbstractService
{
    public function throwValidationErrors(ValidationInterface $entity)
    {
        $validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $exception = new ValidatorException;
            $exception->errors = $errors;
            throw $exception;
        }
    }
}
