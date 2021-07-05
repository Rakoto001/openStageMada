<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\HttpFoundation\Session\SessionInterface; 

    /**
      * @Route("/advert")
      */
class IaController extends AbstractController
{
	/**
	 * @Route("/view/{id}", name="ia_advert_view")
	 **/
	public function view($id){
		 return $this->render('Advert/index.html.twig', ["id"=>$id]);
	}










	/**
	 * @Route("/add", name="ia_advert_add")
	 **/

		public function add(Request $req){
  		
  		  if ($req->isMethod('POST')){
  		  	$this->addFlash('annonceadd', "CECI EST UNE FORMULAIRE");
  		  	return $this->redirectToRoute('ia_advert_view',["id"=>5] );
  		   
  		   }

  		   
  		   return $this->render('Advert/add.html.twig');
		}














     /**
      * @Route("/{page}", name="ia_advert_page",
      *requirements={"page"="\d+"},
      *defaults={"page"=1})
      */
	public function index($page){
	 if($page<1){
	 	throw $this->createNotFoundException("Error Processing Request" .  $page );
	 	
	 }
	return $this->render('Advert/index.html.twig');


	}


	/**
      * @Route("/edit/{id}", name="ia_advert_edit",
      *requirements={"page"="\d+"})
      */

		public function edit($id,Request $req){
			if(isMethod('POST')){
				$this->AddFlash('annonceedit', "Ceci est une annonce dans edit");
			return $this->redirectToRoute('ia_advert_view', ["id"=>5]);
			}
		return $this->render('Advert/edit.html.twig');

		}



	 /**
      * @Route("/delete/{id}", name="ia_advert_delete",
      *requirements={"page"="\d+"})
      */
	 public function delete($id){


	 	return $this->render('Advert/delete.html.twig');

	 }


	}

?>