<?php

namespace SMO\PlatformBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends EntityRepository
{
    public function myFindAll()
    {
        return $this
            ->createQueryBuilder()
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function myFind($id)
    {
        $qd = $this->createQueryBuilder('a');
        
        $qd
            ->where('a.id = :id')
              ->setParameter('id', $id)
        ;
        
        return $qd
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function findByAuthorAndDate($author, $year)
    {
        $qd = $this->createQueryBuilder();
        
        $qd
            ->where('a.author = :author')
              ->setParameter('author', $author)
            ->andWhere('a.date < :year')
              ->setParameter('year', $year)
            ->orderBy('a.date', 'DESC')
        ;
        
        return $qd
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function getAdvertWithApplications($id)
    {
        $qd = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.applications', 'app')
            ->addSelect('app')
            ->where('a.id = :id')
              ->setParameter('id', $id)
        ;
        
        return $qd
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function getAdvertWithCategeries(array $categoryName)
    {
        $qd = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.categories', 'cat', 'WITH', 'cat.name IN :categoryName')
              ->setParameter('categoryName', $categoryName)
            ->addSelect('cat')
        ;
        
        return $qd
            ->getQuery()
            ->getResult()
        ;
    }
    
    
    
    
    
    
    
    
    
    
    
    
}
