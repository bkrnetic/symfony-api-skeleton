<?php

namespace App\Factory;

use App\Entity\EntityInterface;
use App\Exception\VerboseExceptionInterface;
use App\Service\ValidatorService;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\Serializer\SerializerInterface;
use function count;
use function is_a;
use function is_array;

class EntityFactory
{
    private $serializer;
    private $validator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorService $validator
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param $data
     * @param string $class
     * @param array $groups
     * @param array $context
     * @return EntityInterface
     * @throws VerboseExceptionInterface
     */
    public function create($data, string $class, array $groups = [], array $context = []): EntityInterface
    {
        if (!$data instanceof JsonSerializable && !is_array($data)) {
            throw new InvalidArgumentException(
                sprintf('Object must be array or implement %s', JsonSerializable::class)
            );
        }

        if (!is_a($class, EntityInterface::class, true)) {
            throw new InvalidArgumentException(
                sprintf('Param $class must implement %s', EntityInterface::class)
            );
        }

        $data = json_encode($data);

        if (count($groups)) {
            $context['groups'] = $groups;
        }

        /** @var EntityInterface $entity */
        $entity = $this->serializer->deserialize($data, $class, 'json', $context);

        $groups[] = 'default';

        $this->validator->validate($entity, $groups);

        return $entity;
    }
}