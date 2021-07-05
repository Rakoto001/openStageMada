<?php

namespace App\Controller\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserAjaxController extends Controller{


    /**
     * function that list all users on datatable
     */
    public function userList(Request $request): JsonResponse
    {
    $alls = $request->request->all();
    $userService = $this->container->get('user.service');


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


    $listAnnonces = $userService->userList($params);
    $results = $listAnnonces['datas'];
    $dataLists = ['data' => $results];
        return new JsonResponse($dataLists);
    }

    /**
     * delete user object
     */
    public function delete():Response
    {
        $userService = $this->container->get('user.service');
        $id = $_GET['id'];
    
        $userService->removeUser($id);
     
    return new JsonResponse([]);
        
        
    }


    public function deleteAvatar()
    {
        $userService = $this->container->get('user.service');
        $id = $_POST['id'];
        $userService->deleteAvatar($id);
       
        return $this->redirectToRoute('user_list');

    }

  

}