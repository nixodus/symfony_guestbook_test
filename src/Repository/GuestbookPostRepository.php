<?php

namespace App\Repository;

use App\Entity\GuestbookPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GuestbookPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuestbookPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuestbookPost[]    findAll()
 * @method GuestbookPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestbookPostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GuestbookPost::class);
    }

     /**
      * @return GuestbookPost[] Returns an array of GuestbookPost objects
      */

    public function getEnabledList()
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('g.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?GuestbookPost
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
