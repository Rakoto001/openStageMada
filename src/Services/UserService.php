<?php

namespace App\Services;

use App\Entity\User;
use router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Services\BaseService;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService extends BaseService{
    protected $manager;
    protected $container;
    private $encoder;

    //pour le choix du role
    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_USER = "ROLE_USER";
    public const ROLES = [self::ROLE_ADMIN => 'Admin',
                         self::ROLE_USER => 'Utilisateur'];

    //pour le choix du gender
    public const HOMME = 'MALE';
    public const FEMME = 'FEMALE';
    public const USER_GENDER = [self::HOMME => 'Homme',
                                self::FEMME => 'Femme',
                                ];

    //pour le status
    public const ACTIVE = '1';
    public const DESACTIVE = '0';
    public const USER_STATUS = [self::ACTIVE => 'Acvité',
                                self::DESACTIVE => 'Désactivé'];



    public function __construct(EntityManagerInterface $_manager, UserPasswordEncoderInterface $encoder, ContainerInterface $_container){
        $this->manager = $_manager;
        $this->container = $_container;
        $this->encoder = $encoder;
    }

    /**
     * get the user repos
     */
    public function getRepository()
    {

        return $this->manager->getRepository(User::class);
    }

    /**
     * registration and check if user already exist ro not = add and edit
     */
    public function userRegistration( $_params = [])
    {
        $nativeUploadService = $this->container->get('native.upload.service');
        $id = isset($_params['id']) ? $_params['id'] : '0' ;
       // $id = isset($_annonces['id']) ? $_annonces['id'] : '0';
       $password = isset($_annonces['password']) ? $_annonces['password'] : '123456';
        $username = $_params['username'];
        $lastname = $_params['lastname'];
        $firstname = $_params['firstname'];
        $email = $_params['email'];
        $gender = $_params['gender'];
        $status = $_params['status'];
        $roles = $_params['roles'];

        if(empty($id)){
           // dd('ici');
            $user = new User();

        }else{
            $user = $this->getById($id);

        }

        $user->setPassword($this->encoder->encodePassword($user, $password));

        $user->setUsername($username);
        $user->setLastname($lastname);
        $user->setFirstname($firstname);
        $user->setEmail($email);
        $user->setGender($gender);
        $user->setRoles([$roles]);
        $user->setStatus($status);
       // dump(count($_FILES['userAvatar']['name']) );

        if(isset($_FILES['userAvatar']) && !empty( $_FILES['userAvatar']['name'] )){
        
         $userImagePath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_user_avatar');
         $nativeUploadService->makePath($userImagePath);
         $imageAvatar =  $nativeUploadService->upload($userImagePath, 'userAvatar');
         $user->setAvatar($imageAvatar);

         $this->save($user);

        }

        // dd($user);
        $this->save($user);


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
    public function userList($_params)
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
                $rows =  [$avatarImage,
                                $user->getUsername(), 
                                $user->getEmail(),
                                $userAction
                                //'<a href="'.$this->generateUrl($annonce, 'delete').'"> <i class="fa fa-trash-o"></i> </a>'
                        ];

            }
    

            $result[] = $rows;
    
    }
    //  $dataResults = [$titre, $description];
        return ['datas' => $result];
    }


    /**
     * function find
     */
    public function find($_params, $_offsetAndLimit = array())
    {
        //_offsetAndLimit
        $query = $this->getRepository()->createQuery($_params);
        $query = $this->getRepository()->findByFilter($_params, $query);
        $query = $this->getRepository()->addLimit($_offsetAndLimit, $query);
        $query = $this->getRepository()->orderBy($query);

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

      /**
       * delete image from uploaded image path
       */
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

        return $this->save($user);
      }

}