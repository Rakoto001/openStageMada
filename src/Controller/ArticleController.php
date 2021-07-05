<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//après installation de TWIG on doit mettre extends AbstractController
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//pour obtenir le nom et les params des toutes
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController{



//Utilisation des params spéciaux
 /**
  * @route("/article/{_locale}/search.{_format}",
  *locale="en",
  *format="html",
  *name="article_local",
  *requirements ={"_locale":"en|fr", "_format":"html"})
  */


 public function show(Request $req){
 	$route = $req->attributes -> get('_route');
  $http  = $req->server     -> get('HTTP_HOST');
  $page  = $req->headers    -> get('content_type');
 	
 	return $this->render('home/show.html.twig', ["route" => $route,
                                               "http"  => $http, 
                                               "page"  => $page]);
 }


// declaration route par defaut
 /**
  * @route("/article/menu/{id}",
  * requirements= {"id"="\d+"},
  *defaults={"id"=1, 
  *"title": "Hello world"})
  */
 	public function menu(){
 		return $this->render('home/menu.html.twig');
 	}

 	/**
     * @route("/blog", name="blog_list")
     */
 	public function list(){
 		$articles = $this->generateUrl('article_local');
 		return new Response("<html> <body> On a fait une generation des URL par la methode generateUrl , et on a obtenu l'url : $articles </body> </html>");
 		
 	}


// refa le mbola tsy ni install TWIG 
 /*
 public function number(){
 	$number = random_int(1,20);
 	return new Response("<html> <body> Vous ête dans le hall numero " . $number ." </body></html>");
 }

*/
 



}


?>