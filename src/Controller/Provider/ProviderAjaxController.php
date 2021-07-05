<?php
namespace App\Controller\Provider;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProviderAjaxController extends Controller{

    public function listProviderTable(Request $request): JsonResponse
    {
        $providerService = $this->container->get('provider.service');
        $alls            = $request->request->all();
        $offset          = $alls['start'];
        $limit           = $alls['length'];
        $search          = $alls['search'];
        $page            = $alls['page'];
        $query           = $alls['search']['value'];

        //les paramètres
        $params = [];
        $params['offset'] = $offset;
        $params['length'] = $limit;
        $params['search'] = $search;
        $params['query']  = $query;
        $params['page']   = $page;
        $params['list']   = isset($alls['list']) ? $alls['list'] : 1 ;
    
        //$results = [];
    
        $resultProviderLists = $providerService->providerList($params);
        $results = $resultProviderLists['datas'];    
        $dataLists = ['data' => $results];
         
        return new JsonResponse($dataLists) ;

    }
     /**
     * delete provider
     * route delete_provider
     */
    public function deleteProvider(Request $request)
    {

        $providerService = $this->container->get('provider.service');
                   $alls = $request->request->all();
                     $id = $alls['id'];
        $providerService->delete($id);
         return new JsonResponse();
    }

}