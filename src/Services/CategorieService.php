<?php
namespace App\Services;

use router;

use App\Entity\Category;
use App\Services\BaseService;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;


class CategorieService extends BaseService {

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
        return $this->manager->getRepository(Category::class);
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
    public function categoryList($_params = []){
        $categories = $this->find($_params)->getResult();
        $renderResults = [];

        foreach($categories as $categorie){
            //$img = $categorie->getImage();
            $name = $categorie->getName();
            $content = $categorie->getContent();
            $status = $categorie->getStatus();

            $urlAction = '<a href="'.$this->generateUrl($categorie, 'edit').'"><i class="fa fa-edit"></i> </a>';
            $urlAction .= '<a href="javascript:;" url-action="'.$this->generateUrl($categorie, 'delete').'" data-id="'.$categorie->getId().'" class="delete-category" > <i class="fa fa-trash-o"></i> </a>';
           
            //pour le status
            if( $status == 1){
                
                $renderStatus = '<p><span class="badge-dot badge-brand badge-success"></span> Activé</p>';
            }else{
                $renderStatus = '<p><span class="badge-dot badge-brand badge-success"></span> Desactivé</p>';

            }

            //pour l'iconde d'image
            if(!empty($categorie->getImage())){
                $image = '<img src="/uploads/bo/category/'.$categorie->getImage().'" width="30"/>';
                    
                }else{
                $image = '<img src="/uploads/default/coverDefault.png" width="30"/>';
    
                }
            $rows = [ $image,
                           $name,
                           $content,
                           $renderStatus,
                           $urlAction];
        $renderResults[] = $rows;

        }
        
        return ['datas' => $renderResults];
    }

    /**
     * function that generate te ursl edit oe delete
     */
    public function generateUrl($_categorie, $_option )
    {
        $url = $this->container->get('router');

        if($_option == 'delete'){
           
            $urlAction = $url->generate('delete_category', ['id' => $_categorie->getId()]) ;
        }else{
        $urlAction = $url->generate('category_edit',['id' => $_categorie->getId()] );
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
     * check if categ already exist and edit or add new
     * 
     */
    public function checkCategory($_category = [])
    {
        $nativeService = $this->container->get('native.upload.service');
        $isNew = true;
        
        $id = isset($_category['id']) ? $_category['id'] : '0';
        $name = $_category['name'];
        $content = $_category['content'];
        $status = $_category['status'];
        //$coverImage = $_annonces['coverImage'];

        if(empty($_category['id'])){
            $category = new Category();
           // $isNew = false;
        }else{

            $category = $this->getById($id);
            
        }
        $category->setName($name);
        $category->setContent($content);
        $category->setStatus($status);
        $category->setUpdatedAt(new \DateTime());
        $this->manager->persist($category);
        $this->manager->flush();
        if(isset($_FILES['categorieImage']) && !empty($_FILES['categorieImage']['name']) > 0){
            $imageFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_category_image');
            $nativeService->makePath($imageFullPath);
            $filename = $nativeService->upload($imageFullPath,'categorieImage');
            //dd($category);
            $category->setImage($filename);

            $this->save($category) ;
        }
        

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
        $imageName = $categorie->getImage();
        $imageFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_category_image')."/".$imageName;
        
        if( file_exists($imageName) ){
            unlink($imageFullPath);

        }

        return $this->removeData($categorie);

    }



    /**
     * front- list all the categories
     */
    public function listAll($_more)
    {
        
        $listCategories = $this->getRepository()->findByStatus($_more);
        return $listCategories;

    }
  


}