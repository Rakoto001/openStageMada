<?php
namespace App\Controller\Front;
//après installation de TWIG on doit mettre extends AbstractController
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//pour obtenir le nom et les params des toutes
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller{

	/**
	 * function index for the main app
	 */
 	public function index(){
		$categoryService = $this->container->get('category.service');
		$annonceService = $this->container->get('annonce.service');
		$more = false;
		$listCategories = $categoryService->listAll($more);
		$listAnnonces = $annonceService->listAllAnonce($more);
 		return $this->render('fo/fo-main.html.twig', [
							  'listCategories' => $listCategories,
							  'listAnnonces' => $listAnnonces,
													 ]);
		 
	 }
	
 	





}


?>