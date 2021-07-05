<?php

namespace  App\Controller\User;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends controller{

    public function __construct()
    {

    }


    public function register(Request $request): Response
    {
        $userService = $this->container->get('user.service');

        $isEdit = false; 
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
       //  $user->setRoles('0');

        // dd($user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()){
        $alls = $request->request->All();
        $params = $alls['user'];

        try {

               $userService->userRegistration($params);

            } catch(\Doctrine\DBAL\DBALException $e)
            {
                //gestion erreur quand le email ou username est déjà utilisé
                $this->addFlash('error', 'utilisateur dejà existant');
                return $this->redirectToRoute('user_register');
            }

        $this->addFlash('success', 'utilisateur bien enregistré');
        return $this->redirectToRoute('home_bo');


        }


        return $this->render('bo/user/index.html.twig', [
                             'userForm' => $userForm->createView(),
                             'isEdit' => $isEdit,

        ]);
    }


    /**
     * list user
     */
    public function listUser()
    {
        return $this->render('bo/user/list.html.twig');
    }

    /**
     * edit user by ID
     */
    public function edit(Request $request){
        $isEdit = true;

        $params = [];
        $userService = $this->container->get('user.service');

        $id = $_GET['id'];
        $user =  $userService->findById($id);
        $userAvatar = $user->getAvatar();
        //dd($userAvatar);
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()){
            $alls = $request->request->all();
            $params = $alls['user'];
            $params['id'] = $_GET['id'];

            $userService->userRegistration($params);

            $this->addFlash('success','Modification avec succès');

            return $this->redirectToRoute('home_bo');

        }
        //  $user->setRoles('0');
 
         // dd($user);
 

         return $this->render('bo/user/index.html.twig',[
                                 'userForm' => $userForm->createView(),
                                 'user' => $user,
                                 'isEdit' => $isEdit,
                                 'userAvatar' => $userAvatar,
         ]
         );

    }


    /**
     * generate admin account
     */
    public function generateAdmin()
    {
        $userService = $this->container->get('user.service');
        $userService->defaultUserRegistration();

        $this->addFlash('success', 'Admin genéré avec succès');

        return $this->redirectToRoute('app_login');


    }

}