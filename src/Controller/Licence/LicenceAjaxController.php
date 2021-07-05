<?php
namespace App\Controller\Licence;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LicenceAjaxController extends Controller{

    public function listLicenceTable(Request $request): JsonResponse
    {

        $licenceService = $this->container->get('licence.service');

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
    
        $resultLicenceLists = $licenceService->licenceList($params);
        $results = $resultLicenceLists['datas'];    
        $dataLists = ['data' => $results];
         
        return new JsonResponse($dataLists) ;

    }
     /**
     * delete licence
     */
    public function deleteLicence(Request $request)
    {
        $licenceService = $this->container->get('licence.service');

        $alls = $request->request->all();
        $id = $alls['id'];
         $licenceService->delete($id);

         return new JsonResponse();
    }

}