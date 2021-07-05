<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//après installation de TWIG on doit mettre extends AbstractController
use Symfony\Component\Routing\Annotation\Route;
//pour obtenir le nom et les params des toutes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController{

	/**
	 * function index for the main app
	 */
 	public function index(){

 		return $this->render('/main.html.twig');
	 }
	 
	 /**
	  * login app
	  */
	 public function login(AuthenticationUtils $authenticationUtils, Request $request)
	 {
		 $error = $authenticationUtils->getLastAuthenticationError();
		 $lastLoged = $authenticationUtils->getLastUsername();
		 if( $this->isGranted('IS_AUTHENTICATED_FULLY') ){

			 return $this->redirectToRoute('home_bo');
		 }


		return $this->render('/login.html.twig', ['error' => $error]);
	 }


	 /**
	  * logout app
	  */
	 public function logout()
	 {

		// return $this->render
	 }

 	





}


?>