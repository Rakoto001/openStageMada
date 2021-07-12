<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\FetchUtils;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
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
       $qb =  $this->createQueryBuilder('c');

       return $qb;

    }



    /**
     * function filter result from createQuery
     */
    public function findByFilter($_params, $_query)

    {
        if( array_key_exists('query', $_params) && !empty($_params['query']) ){
            $_query->andWhere('c.name LIKE :name')
                   ->orWhere('c.content LIKE :content')
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
        $_query->orderBy('c.id', 'ASC');
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

             $_query->where('c.name LIKE :name')
                    ->setParameter('name' , $_params['query'].'%');
            }

     return $_query;
       
               
    }

    // /**
    //  * give category join announce with status = 1
    //  */
    public function findByStatus($_more)
    {
        $connexion = $this->getEntityManager()->getConnection();

        $sql =
        '
        SELECT cat.id,cat.name, cat.image, count(annonce.id) nbAnnonce 
        FROM category cat
        RIGHT JOIN annonce 
        ON annonce.category_id =cat.id 
        WHERE status = 1 
        GROUP BY cat.id 
        ORDER BY nbAnnonce DESC 
        
        ';
        if($_more == false){
         $sql .= 'LIMIT 4';
   

        }

            // $sql = 'SELECT * FROM category 
            //         WHERE category.status = 1 
            //         ORDER BY category.id DESC LIMIT 4
            //         ';
            // $sql = `SELECT *, count(annonce.id) nbAnnonce 
            //         FROM category 
            //         JOIN annonce 
            //         ON annonce.category_id =category.id 
            //         WHERE status = 1 
            //         GROUP BY category.id 
            //         ORDER BY nbAnnonce DESC 
            //         LIMIT 4`;
           
            $statement = $connexion->prepare($sql);
            $statement->execute();

            $results = $statement->fetchAll();

        
        // $query = $this->createQuery();
        
        return $results;

    }

   public function findCategoryActive($_status)
   {
     return  $qb = $this->createQuery()

                  ->andWhere('c.status = :status')
                  ->setParameter('status', $_status)
                  ->getQuery()
                  ->getResult();
   }

   public function reverseStatus()
   {
       
   }
}
