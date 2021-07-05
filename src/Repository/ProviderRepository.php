<?php

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Provider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findAll()
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    
      /**
     * createQuery function  
     */
    public function createQuery($_params =null)
    {

       $qb =  $this->createQueryBuilder('p');

       return $qb;
    }

    /**
     * function that order the result from createQuery
     */
    public function orderBy($_query)
    {
        $_query->orderBy('p.id', 'ASC');

        return $_query;
    }

    /**
     * search by criterias
     */
    public function searchbyCryterias($_query, $_params){
        if(array_key_exists('query',$_params) && !empty($_params['query']) ){
             $_query->where('p.name LIKE :name')
                    ->setParameter('name' , $_params['query'].'%');
            }

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
     * find providers by status
     */
    public function findByStatus($_more)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT pro.id, pro.name, pro.image, 
        count(annonce.id) nbAnnonce 
        FROM `provider` pro 
        JOIN annonce 
        ON annonce.provider_id=pro.id 
        WHERE status = 1
        GROUP BY pro.id";

        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        // dd($results);

        return $results;



    }

    
    
    // /**
    //  * @return Provider[] Returns an array of Provider objects
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

    /*
    public function findOneBySomeField($value): ?Provider
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
