<?php
namespace App\Controller\Category;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryAjaxController extends Controller{

    /**
     * list category on datatable
     */
    public function listCategoryTable(Request $request): JsonResponse
    {
        $categoryService = $this->container->get('category.service');
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
        $resultCategorieLists = $categoryService->categoryList($params);
        $results = $resultCategorieLists['datas'];    
        $dataLists = ['data' => $results];
         
        return new JsonResponse($dataLists) ;

    }

     /**
     * delete category
     */
    public function deleteCategory(Request $request)
    {
        $categoryService = $this->container->get('category.service');
        $alls = $request->request->all();
        $id = $alls['id'];
        $categoryService->delete($id);

         return new JsonResponse();
    }

}