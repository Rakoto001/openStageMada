<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//après installation de TWIG on doit mettre extends AbstractController
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//récupération des services par les interfaces
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NumberController extends AbstractController{

 /**
  * @route("/home/{number}", name="homepage")
  */

 public function number(loggerInterface $logger){
 	$logger->info('Vous ête bien connecté');
 	$num = random_int(1, 10);

 	return $this->render('home/number.html.twig', ["number"=>$num]);
 }






// REDIRECTION VERS LA FUNCTION NUMBER
 /**
  * @route("/home/")
  */
 public function redirectionToHome(){
 	return $this->redirectToRoute("homepage", ["number"=>8]);
 }

// ROUTE EN UNE SEULE PARAM avec erquirements et param par défaut
 /**
  * @route("/home/local/{i<\d+>?0}" , name="blog_local",)
  */

//Déclaration route avec requirements 
 /*
  * @route("/home/local/{i}" , name="blog_local", requirements ={"i=\d+"})
  */

 public function local(int $i){
 	//Dès que vous ajoutez un paramètre à une route, il doit avoir une valeur.
 	// c'est ce qu'on appelle des paramètres facultatifs 
 	//si $i=1 si l'utilisateur vas visiter /home/local
 	
 	return $this->render('home/local.html.twig', ["i"=>$i]);
 }







// gestion erreur 404
 /**
  * @route("/home/index/page/" , name="bhome_index")
  */
 	public function index(){
 		$page=7;
 		if(!$page){
 			throw $this->createNotFoundException('page not found');

 		}
 		return $this->render('home/menu.html.twig');
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