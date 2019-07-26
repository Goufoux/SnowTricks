<?php

namespace App\Repository;

use App\Entity\VideoLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VideoLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoLink[]    findAll()
 * @method VideoLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VideoLink::class);
    }

    // /**
    //  * @return VideoLink[] Returns an array of VideoLink objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VideoLink
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
