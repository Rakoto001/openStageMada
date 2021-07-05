<?php
namespace App\Controller\Front;
//après installation de TWIG on doit mettre extends AbstractController
use Knp\Component\Pager\PaginatorInterface;
//pour obtenir le nom et les params des toutes
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProviderController extends Controller{
	private $paginator;

	public function __construct(PaginatorInterface  $_paginator)
	{
		$this->paginator = $_paginator;
	}

	/**
	 * list All providers
	 */
	public function listProvider()
	{
		$more = false;
		$providerService = $this->container->get('provider.service');
		$providers = $providerService->listAll($more);

		return $this->render('fo/provider/provider-list.html.twig',
		 [
			 'providers' => $providers
		 ]
		);
	}
	
	/**
	 * function list the provider
	 */
	public function listAnnonceByProvider(Request $request){
		$id = $request->query->get('id');
		$annonceService = $this->container->get('annonce.service');
		$annonces = $annonceService->listAnonceByProvider($id);
		$annoncePaginator =  $this->paginator->paginate(
							 $annonces,$request->query->getInt('page', 1),3 );
		
		 return $this->render('fo/provider/provider-annonce-list.html.twig', 
		 [
			 'annonces'     => $annoncePaginator,
		 ]
		);
	 }


}


?>