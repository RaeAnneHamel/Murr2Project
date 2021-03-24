<?php

namespace App\Repository;

use App\Entity\PickUp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PickUp|null find($id, $lockMode = null, $lockVersion = null)
 * @method PickUp|null findOneBy(array $criteria, array $orderBy = null)
 * @method PickUp[]    findAll()
 * @method PickUp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PickUpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PickUp::class);
    }

    public function findPickupById($value): ?PickUp
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}