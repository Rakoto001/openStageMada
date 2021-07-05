<?php
namespace App\Services;

use router;

use App\Entity\Licence;
use App\Services\BaseService;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;


class LicenceService extends BaseService {

   

    public function __construct(EntityManagerInterface $_manager, ContainerInterface $_container)
    {
        $this->manager = $_manager;
        $this->container = $_container;
    }

    /**
     * get Categ Repos
     */
    public function getRepository()
    {
        return $this->manager->getRepository(Licence::class);
    }

    /**
     * get All categ
     */
    public function getAlls()
    {
        return $this->getRepository()->findAll();
    }


    /**
     * list category on dtatble
     */
    public function licenceList($_params = []){
        $licences = $this->find($_params)->getResult();
        $renderResults = [];

        foreach($licences as $licence){
            //$img = $categorie->getImage();
            $name = $licence->getName();
            $content = $licence->getContent();
            $status = $licence->getStatus();
            $url = $licence->getUrlOfficial();

            $urlAction = '<a href="'.$this->generateUrl($licence, 'edit').'"><i class="fa fa-edit"></i> </a>';
            $urlAction .= '<a href="javascript:;" url-action="'.$this->generateUrl($licence, 'delete').'" data-id="'.$licence->getId().'" class="delete-licence" > <i class="fa fa-trash-o"></i> </a>';
           
            //pour le status
            if( $status == 1){
                
                $renderStatus = '<p><span class="badge-dot badge-brand badge-success"></span> Activé</p>';
            }else{
                $renderStatus = '<p><span class="badge-dot badge-brand badge-success"></span> Desactivé</p>';

            }

           
            $rows = [
                           $name,
                           $content,
                           $renderStatus,
                           $url,
                           $urlAction
                    ];
        $renderResults[] = $rows;

        }
        
        return ['datas' => $renderResults];
    }

    /**
     * function that generate te ursl edit oe delete
     */
    public function generateUrl($_licence, $_option )
    {
        $url = $this->container->get('router');

        if($_option == 'delete'){
           
            $urlAction = $url->generate('delete_licence', ['id' => $_licence->getId()]) ;
        }else{
        $urlAction = $url->generate('licence_edit',['id' => $_licence->getId()] );
       }
        return $urlAction;

    }

    /**
     * function find
     */
    public function find($_params)
    {
        $limit = $_params['length'];
        $offset = $_params['offset'];
       /// $limits = $_params['length'];
        $query = $this->getRepository()->createQuery($_params);
        $query = $this->getRepository()->orderBy($query);
        $query =$this->getRepository()->searchbyCryterias($query, $_params);

        $query = $this->getRepository()->addLimit($query, $limit, $offset);



    return $query->getQuery();

    }

    /**
     * check if licence already exist and edit or add new
     * 
     */
    public function checkLicence($_licence = [])
    {
       
        $isNew = true;
        
        $id = isset($_licence['id']) ? $_licence['id'] : '0';
        $name = $_licence['name'];
        $content = $_licence['content'];
        $status = $_licence['status'];
        $url = $_licence['urlOfficial'];
        //$coverImage = $_annonces['coverImage'];

        if(empty($_licence['id'])){
            $licence = new Licence();
           // $isNew = false;
        }else{

            $licence = $this->getById($id);
            
        }
        $licence->setName($name);
        $licence->setContent($content);
        $licence->setStatus($status);
        $licence->setUrlOfficial($url);
        $this->manager->persist($licence);
        $this->manager->flush();

    }

    /**
     * find obj by Id
     */
    public function findOneByID($_id)
    {
        return $this->getById($_id);


     }

    public function delete($_id)
    {
        $categorie = $this->findOneByID($_id);
        

        return $this->removeData($categorie);

    }
  


}