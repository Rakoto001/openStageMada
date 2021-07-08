<?php

namespace App\Repository;

use App\Entity\Licence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Licence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Licence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Licence[]    findAll()
 * @method Licence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LicenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Licence::class);
    }

    // /**
    //  * @return Licence[] Returns an array of Licence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Licence
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */



    
      /**
     * createQuery function on annonce 
     */
    public function createQuery($_params =null)
    {
       $qb =  $this->createQueryBuilder('l');

       return $qb;

    }



    /**
     * function filter result from createQuery
     */
    public function findByFilter($_params, $_query)

    {
        if( array_key_exists('query', $_params) && !empty($_params['query']) ){
            $_query->andWhere('l.name LIKE :name')
                   ->orWhere('l.content LIKE :content')
                   ->setParameter('content', $_params['query'].'%')
                   ->setParameter('name',$_params['query'].'%');

        }
        
        return $_query;
    }


    /**
     * function that order the result from createQuery
     */
    public function orderBy($_query)
    {
        $_query->orderBy('l.id', 'ASC');
        return $_query;

    }

    /**
     * add limit and offset to the result
     */
    public function addLimit($_query, $limit, $offset )
    {
      
       $_query->setMaxResults($limit)
              ->setFirstResult($offset);

       return $_query;
    }

    /**
     * search by criterias
     */
    public function searchbyCryterias($_query, $_params){
        //dd($_query->getQuery()->getResult());


        if(array_key_exists('query',$_params) && !empty($_params['query']) ){

             $_query->where('l.name LIKE :name')
                    ->setParameter('name' , $_params['query'].'%');
            }

     return $_query;
       
               
    }

    // /**
    //  * give category by status = 1
    //  */
    public function findByStatus()
    {
         $qb = $this->createQuery()
                    ->andWhere('l.status = 1')
                    ->orderBy('l.id', 'ASC')
                    ->getQuery();
        $results = $qb->getResult();

        return $results;

    }


    
}
