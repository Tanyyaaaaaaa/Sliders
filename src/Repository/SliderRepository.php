<?php

namespace App\Repository;

use App\Entity\Slider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slider[]    findAll()
 * @method Slider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slider::class);
    }

    /**
     * @return Slider[] Returns an array of User objects
     */

    public function findActive($categoryId)
    {
        return $this->createQueryBuilder('s')
            ->join('App:SliderCategory', 'sc', 'WITH', 'sc.slider = s.id')
            ->andWhere('s.id != 0')
            ->andWhere('sc.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
