<?php
namespace App\Controller\Annonce;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//après installation de TWIG on doit mettre extends AbstractController
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


//ININERAIRES LOCALISES

class AnnonceAjaxController extends Controller{

   /**
    *list annonce on dataTable 
    */
 public function listAnnonceTable(Request $request): JsonResponse
 {
    $annonceService = $this->container->get('annonce.service');


    $alls = $request->request->all();
    $offset = $alls['start'];
    $limit = $alls['length'];
    $search = $alls['search'];
    $page = $alls['page'];
    $query = $alls['search']['value'];
    //les paramètres
    $params = [];
    $params['offset'] = $offset;
    $params['length'] = $limit;
    $params['search'] = $search;
    $params['query'] = $query;
    $params['page'] = $page;
    $params['list'] = isset($alls['list']) ? $alls['list'] : 1 ;

    //$results = [];

    $listAnnonces = $annonceService->annonceList($params);
   //  dd($listAnnonces);
    $dataAnnonces = $listAnnonces['datas'];
    $totAnnonces = $listAnnonces['count'];

    $dataLists = ['data' => $dataAnnonces, 'recordsTotal' => $totAnnonces, 'recordsFiltered' => count($listAnnonces)];
     
    return new JsonResponse($dataLists) ;
 }

 /**
  * delete annonce from database
  */
 public function deleteAnnonce(Request $request)
 {
   $annonceService = $this->container->get('annonce.service');

    $alls = $request->request->all();
    $id = $alls['id'];
    
    $annonce = $annonceService->findAnnonce($id);
    $annonceService->delete($annonce);

    return new JsonResponse();

 }






}


?>