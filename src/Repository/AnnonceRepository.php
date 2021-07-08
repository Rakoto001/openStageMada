<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
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
     * findall join category 
     */
    public function findAlls($_query)
    {
        //    $qb = $this->createQueryBuilder('a')
        //     ->innerJoin('a.articleLangues','al', 'WITH', 'al.article = a')
        //     ->leftJoin('a.category','c')
        //     ->addSelect('al')
        //     ->addSelect('c')
        // ;
        // dd($_params);
      $_query->leftJoin('a.category', 'categ')
             ->addSelect('categ');

                return $_query;
    }


    /**
     * function that order the result from createQuery
     */
    public function orderBy($_query, $_limit)
    {
        $_query->orderBy('a.id', 'DESC');

        return $_query;

    }

    /**
     * add limit and offset to the result
     */
    public function addLimit($_params , $_query)
    {
      
       $_query->setMaxResults($_params['length'])
              ->setFirstResult($_params['offset']);

       return $_query;
    }
    /*$em->createQuery("select ... from ... where ...")
       ->setMaxResults($limit)
       ->setFirstResult($offset)*/


    /**
     * find annonce by id category
     */
    public function findAnnonceByCateg($_id)
    {
       // $connexion = $this->getEntityManager()->getConnection();
        // $sql = 
        //         "SELECT * FROM `annonce` 
        //         JOIN category ON annonce.category_id=category.id 
        //         WHERE category.id= $_id 
        //         ORDER BY annonce.id ASC";
        // $statement = $connexion->prepare($sql);
        // $statement->execute();
        // $results = $statement->fetchAll();
        // pour requette doctrine
        $qb = $this->createQuery()
                    ->leftJoin('a.category', 'c')
                    ->addSelect('c')
                    ->where('c.id = :id')
                    ->setParameter('id', $_id)
                    ->getQuery();
        $results= $qb->getResult();
        
        return $results;

    }

    /**
     * take the annonce order by date DESC
     */
    public function findByDate($_more)
    {
        $connexion = $this->getEntityManager()->getConnection();
        $sql = " SELECT * FROM annonce ORDER BY created_at DESC";

        if( $_more ==  false ){
            $sql .=  ' LIMIT 2';
        }
         $statement = $connexion->prepare($sql);
         $statement->execute();
         $results = $statement->fetchAll();

    return $results;
    }

    /**
     * find all announce by the provider ID
     */
    public function findAnnonceByProvider($_id)
    {
        /* équivalent
        SELECT * FROM `annonce` 
        JOIN provider 
        ON annonce.provider_id=provider.id 
        WHERE provider.id = 2 */

        $qb = $this->createQuery()
                    ->leftJoin('a.provider', 'p')
                    ->addSelect('p')
                    ->where('p.id = :id')
                    ->setParameter('id', $_id)
                    ->getQuery();
        $results = $qb->getResult();

        
        return $results;

    }
}
