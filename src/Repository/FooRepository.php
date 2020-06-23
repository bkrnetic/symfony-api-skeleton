<?php

namespace App\Repository;

use App\Entity\Foo;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Foo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Foo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Foo[]    findAll()
 * @method Foo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FooRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Foo::class);
    }
}
