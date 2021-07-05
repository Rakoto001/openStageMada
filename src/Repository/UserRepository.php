<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

   /**
     * createQuery function on user 
     */
    public function createQuery($_params =null)
    {
       $qb =  $this->createQueryBuilder('a');

       return $qb;

    }

    
    /**
     * function filter result from createQuery
     */
    public function findByFilter($_params, $_query)

    {
        if( array_key_exists('query', $_params) && !empty($_params['query']) ){
            $_query->andWhere('a.Title LIKE :title')
                   ->orWhere('a.Description LIKE :description')
                   ->setParameter('title', $_params['query'].'%')
                   ->setParameter('description',$_params['query'].'%');

        }
        
        return $_query;
    }


    /**
     * function that order the result from createQuery
     */
    public function orderBy($_query)
    {
        $_query->orderBy('a.id', 'ASC');

        return $_query;

    }

    /**
     * add limit and offset to the result
     */
    public function addLimit($_params , $_query)
    {
      
       $_query->setMaxResults($_params['limit'])
              ->setFirstResult($_params['offset']);

       return $_query;
    }
}
