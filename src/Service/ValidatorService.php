<?php

namespace App\Service;

use App\Exception\ApiValidationException;
use App\Exception\VerboseExceptionInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;

/**
 * Class ValidatorService
 */
final class ValidatorService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * ValidatorService constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * @param object $object
     * @param array  $groups
     *
     * @throws VerboseExceptionInterface
     */
    public function validate($object, array $groups): void
    {
        $errors = [];

        if (count($validationErrors = $this->validator->validate($object, null, $groups)) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($validationErrors as $error) {
                $errors[$error->getPropertyPath()] = $error->getMessage();
            }

            throw ApiValidationException::create('Validation failed', $errors);
        }
    }
}
