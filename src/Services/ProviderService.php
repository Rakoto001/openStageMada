<?php
namespace App\Services;

use router;

use App\Entity\Provider;
use App\Services\BaseService;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;


class ProviderService extends BaseService {

    protected $manager;
    protected $container;

    //const status

    const ACTIVE = '1';
    const DESACTIVE = '0';
    const CHOICE_STATUS = [self::ACTIVE => 'active',
                self::DESACTIVE => 'desactive'];

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
        return $this->manager->getRepository(Provider::class);
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
    public function providerList($_params = []){
        $providers = $this->find($_params)->getResult();
        $renderResults = [];
        foreach($providers as $provider){
            $name       = $provider->getName();
            $contact    = $provider->getEmail();
            $status     = $provider->getStatus();
            // $urlAction  = '<a href="'.$this->generateUrl($provider, 'edit').'"><i class="fa fa-edit"></i> </a>';
            $urlAction = '<a href="'.$this->generateUrl($provider, 'edit').'"><i class="fa fa-edit"></i> </a>';
            $urlAction .= '<a href="javascript:;" url-action="'.$this->generateUrl($provider, 'delete').'" data-id="'.$provider->getId().'" class="delete-provider" > <i class="fa fa-trash-o"></i> </a>';
            
            //pour le status
            if( $status == 1){
                $renderStatus = '<p><span class="badge-dot badge-brand badge-success"></span> Activé</p>';
            }else{
                $renderStatus = '<p><span class="badge-dot badge-brand badge-success"></span> Desactivé</p>';
            }
            //pour l'iconde d'image
            if(!empty($provider->getImage())){
                $image = '<img src="/uploads/bo/provider/'.$provider->getImage().'" width="30"/>';
                    
                }else{
                $image = '<img src="/uploads/default/coverDefault.png" width="30"/>';
    
                }
            $rows = [ $image,
                      $name,
                      $contact,
                      $renderStatus,
                      $urlAction];

            $renderResults[] = $rows;
        }
        
        return ['datas' => $renderResults];
    }

    /**
     * function that generate te ursl edit oe delete
     */
    public function generateUrl($_provider, $_option )
    {
        $url = $this->container->get('router');
        if($_option == 'delete'){
            $urlAction = $url->generate('delete_provider', ['id' => $_provider->getId()]) ;
        }else{
            $urlAction = $url->generate('provider_edit',['id' => $_provider->getId()] );
       }

        return $urlAction;

    }

      /**
     * function find
     */
    public function find($_params)
    {
        $limit  = $_params['length'];
        $offset = $_params['offset'];
        $query  = $this->getRepository()->createQuery($_params);
        $query  = $this->getRepository()->orderBy($query);
        $query  = $this->getRepository()->searchbyCryterias($query, $_params);
        $query  = $this->getRepository()->addLimit($query, $limit, $offset);

    return $query->getQuery();

    }

    /**
     * check if provider already exist and edit or add new
     * 
     */
    public function checkProvider($_provider = [])
    {
        //suppression du token
        unset($_provider['_token']);

        $nativeService = $this->container->get('native.upload.service');
        $isNew         = true;
        $id            = isset($_provider['id']) ? $_provider['id'] : '0';
        $name          = $_provider['Name'];
        $email         = $_provider['email'];
        $status        = $_provider['status'];
        //$coverImage = $_annonces['coverImage'];
        if(empty($_provider['id'])){
            $provider = new Provider();
           // $isNew = false;
        }else{
            $provider = $this->getById($id);
            
        }
        $provider->setName($name);
        $provider->setEmail($email);
        $provider->setStatus($status);
        // $provider->setUpdatedAt(new \DateTime());
        $this->manager->persist($provider);
        //image provider
        if( isset($_FILES['providerImage']) && !empty($_FILES['providerImage']['name']) ){
            $imageprovidermageFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_provider_image');
            $nativeService->makePath($imageprovidermageFullPath);
            $filename = $nativeService->upload($imageprovidermageFullPath,'providerImage');
            $provider->setImage($filename);

            $this->save($provider) ;
        }
       $this->manager->flush();
    }


    /**
     * delete the provider
     */
    public function delete($_id)
    {
        $provider      = $this->getById($_id);
        $imageName     = $provider->getImage();
        $imageFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_provider_image')."/".$imageName;
        if($imageName != null){
            unlink($imageFullPath);
        }

        return $this->removeData($provider);

    }



    /**
     * front- list all the categories
     */
    public function listAll($_more)
    {
        
        $listProviders = $this->getRepository()->findByStatus($_more);
        return $listProviders;

    }

    
  


}