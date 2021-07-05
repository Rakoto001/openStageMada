<?php

namespace App\Services;

use router;
use App\Entity\Stagiaire;
use App\Services\BaseService;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class StagiaireService extends BaseService{
    protected $manager;
    protected $container;


    //pour le choix du gender
    public const HOMME = 'MALE';
    public const FEMME = 'FEMALE';
    public const STAGIAIRE_GENDER = [self::HOMME => 'Homme',
                                self::FEMME => 'Femme',
                                ];



    public function __construct(EntityManagerInterface $_manager, ContainerInterface $_container){
        $this->manager = $_manager;
        $this->container = $_container;
    }

    /**
     * get the user repos
     */
    public function getRepository()
    {

        return $this->manager->getRepository(Stagiaire::class);
    }

    /**
     * registration and check if user already exist ro not = add and edit
     */
    public function stagiaireRegistration( $_params = [])
    {
        $nativeUploadService = $this->container->get('native.upload.service');
        $id = isset($_params['id']) ? $_params['id'] : '0' ;
        $fullName = $_params['FullName'];
        $sexe = $_params['Sexe'];
        $grade = $_params['Grade'];
        $experience = $_params['Experience'];
        $contact = $_params['Contact'];

        if(empty($id)){
        $stagiaire = new Stagiaire();
        }else{
            $stagiaire = $this->getById($id);

        }

        $stagiaire->setFullName($fullName);
        $stagiaire->setSexe($sexe);
        $stagiaire->setGrade($grade);
        $stagiaire->setExperience($experience);
        $stagiaire->setContact($contact);
         
        if(isset($_FILES['stagiaireDocument']) ){
        
         $documentPaht = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_user_avatar');
         $nativeUploadService->makePath($documentPaht);
         $stagiaireDocument =  $nativeUploadService->upload($documentPaht, 'stagiaireDocument');
         $stagiaire->setCv($stagiaireDocument);
         $this->save($stagiaire);

        }
        $this->save($stagiaire);

    }

    public function defaultUserRegistration()
    {
        $user = new User();
        $user->setPassword($this->encoder->encodePassword($user, '123456'));
        $user->setUsername('admin');
        $user->setLastname('admin');
        $user->setFirstname('admin');
        $user->setEmail('stageAdmin@gmail.com');
        $user->setGender('MALE');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setStatus(1);
        $this->save($user);

        return $user;
        //email - username - gender - roles ADMIN - AVATAR
        //email : stageAdmin@gmail.com - passs:admin2021;
        //test button START -> usercontroler-> generateAdmin() => userService-> defaultUserRegistration()
    }

    /**
     * list all annonces 
     */
    public function annonceList($_params)
    {
        
        $users = $this->find($_params, ['offset' => $_params['offset'],'limit' => $_params['length']])->getResult();   
        $result = [];

        foreach($users as $user){
            $imageFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads';
            //action delete or edit
            $userAction  = '<a href="'.$this->generateUrl($user).'" > <i class="fa fa-edit"></i> </a>';
            $userAction  .= '<a href="javascript:;" ajax-url="'.$this->generateUrl($user, 'delete').'" data-id="'.$user->getId().'" class="delete-user"> <i class="fa fa-trash-o"></i> </a>';
        //  $deleteAction  = '<a href="" class="delete-annonce"> <i class="fa fa-trash-o"></i> </a>';
        

            if(!empty($user->getAvatar())){
            $avatarImage = '<img src="/uploads/bo/user/'.$user->getAvatar().'" width="30"/>';
                
            }else{
            $avatarImage = '<img src="/uploads/default/coverDefault.png" width="30"/>';

            }
            if($_params['page'] == 'user'){
                //dd($annonce->getId());
                $rows =  array( $avatarImage,
                                $user->getUsername(), 
                                $user->getEmail(),
                                $userAction
                                //'<a href="'.$this->generateUrl($annonce, 'delete').'"> <i class="fa fa-trash-o"></i> </a>'
                        );

            }
    

            $result[] = $rows;
    
    }
    //  $dataResults = [$titre, $description];
        return ['datas' => $result];
    }


    /**
     * function find
     */
    public function find($_params, $_offset = array(), $_limit = array())
    {
        $query = $this->getRepository()->createQuery($_params);
        $query = $this->getRepository()->findByFilter($_params, $query);
        $query = $this->getRepository()->addLimit($_params, $query);
        $query = $this->getRepository()->orderBy($query, $_limit);

    return $query->getQuery();

    }



         /**
      * function that generate URL for delete or edit
      */
      public function generateUrl($_annonce, $action = null)
      {
          $url = $this->container->get('router');
          if($action =='delete'){
 
            $result = $url->generate('user_ajax_delete', ['id' => $_annonce->getId()]);
 
          }else{
 
             $result = $url->generate('user_edit', ['id' => $_annonce->getId()]);
 }
      return $result;
      }


      /**
       * function that find object user bu ID
       */
      public function findById($_id)
      {
          return $this->getById($_id);

      }


      /**
       * remove entity from database
       */
      public function removeUser($_id)
      {
          $user = $this->getById($_id);

         return  $this->removeData($user);
      }

      public function deleteAvatar($_id)
      {
          $user = $this->findById($_id);
        $avatarName = $user->getAvatar();

        //suppresssion du fichier dans le upload
        $avatarFilePath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_user_avatar')."/".$avatarName;
        if( file_exists($avatarFilePath) ){

            unlink($avatarFilePath);

        } 
        
        //suppression de l'avatar dans  la db
        $userAvatar = $user->setAvatar('');
        $this->save($user);
      }

      

      
 
   

}