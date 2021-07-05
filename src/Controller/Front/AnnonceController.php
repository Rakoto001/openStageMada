<?php
namespace App\Controller\Front;
//après installation de TWIG on doit mettre extends AbstractController
use Knp\Component\Pager\PaginatorInterface;
//pour obtenir le nom et les params des toutes
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends Controller{

	private $paginator;
	/**
	 * constructor
	 */
	public function __construct(PaginatorInterface  $_paginator)
	{
		$this->paginator = $_paginator;
		
	}

	/**
	 * function list the category
	 */
	public function listAnnonceByCategory(Request $request){
		$id = $request->query->get('id');

		$annonceService = $this->container->get('annonce.service');
		$categoryService = $this->container->get('category.service');
		//get All annonce by their category ID
		$annonces = $annonceService->listAnnonceByCategory($id);
		$categoryName = $categoryService->findOneByID($id)->getName();
//
 		return $this->render('fo/annonce/annonce-category.list.html.twig', [
							 'annonces'     => $annonces,
							 'categoryName' => $categoryName,
		 ]);
	 }

	 /**
	  * give details for each annonce
	  */
	 public function annonceDetails(Request $request)
	 {
		$annonceService = $this->container->get('annonce.service');
		$more = true;
		$id = $request->query->get('id');
		$annonce = $annonceService->findAnnonce($id);
		$recentAnnonces = $annonceService->listAllAnonce($more);
		$annonceWitPaginations = $this->paginator->paginate(
			$recentAnnonces,
			$request->query->getInt('page', 1),3
		);


		return $this->render('fo/annonce/annonce-details.html.twig', [
			'annonce'     => $annonce,
			'recentAnnonces' => $annonceWitPaginations,
		]);
		 
	 }


	 /**
	  * list all annonce
	  */
	 public function annonceList(Request $request)
	 {
		$annonceService = $this->container->get('annonce.service');
		$more = true;
		$listAnnonces = $annonceService->listAllAnonce($more);
		$annonceWitPaginations = $this->paginator->paginate(
			$listAnnonces,
			$request->query->getInt('page', 1),3
		);


		return $this->render('fo/annonce/annonce-list.html.twig', [
			'annonces'     => $annonceWitPaginations,
		]);
		 
		
	 }
	
 	





}


?>