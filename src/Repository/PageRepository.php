<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    // /**
    //  * @return Page[] Returns an array of Page objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findOneBySlug(string $value): ?Page
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.slug = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function SearchByTitle(string $title)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('LOWER(p.title) LIKE LOWER(:val)')
            ->setParameter('val', '%'.$title.'%')
            ->orderBy('a.title', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
