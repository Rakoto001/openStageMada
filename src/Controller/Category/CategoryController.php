<?php

namespace App\Controller\Category;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends Controller{

    public function listCategory()
    {
        // $categoryService = $this->container->get('category.service');
        // $categories = $categoryService->getAlls();


        return $this->render('/bo/category/list.html.twig');
    }

    public function new(Request $request)
    {
        $isEdit = false;

        $categoryService = $this->container->get('category.service');

        $categorie = new Category();
        
         $formCategory = $this->createForm(CategoryType::class, $categorie);
         $formCategory->handleRequest($request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()){
            $alls = $request->request->All();
            $categories = $alls['category'] ;
            $categoryService->checkCategory($categories);

           $flashMessage =  $this->addFlash('success', 'ajout de l\'annonce avec avec success');


        return $this->redirectToRoute('list_category');
    }

       return $this->render('bo/category/index.html.twig', 
                            ['categorieForm' => $formCategory->createView(),
                            'isEdit' => $isEdit] );
    
    
    }

    /**
     * edit the ceteg route : categorie_edit
     */
    public function editCategory(Request $request)
    {
        $categoryService = $this->container->get('category.service');
        $isEdit = true;
        $id = $request->query->get('id');
        $categorie = $categoryService->findOneByID($id);
        $image = $categorie->getImage();

        $formCategory = $this->createForm(CategoryType::class, $categorie);
        $formCategory->handleRequest($request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()){
            $alls = $request->request->All();
            $categories = $alls['category'] ;
            $categories['id'] = $id;

            $categoryService->checkCategory($categories);

            $this->addFlash('success', 'Mise à Jour de la catégorie succès');

        return $this->redirectToRoute('list_category');

        }
        

        return $this->render('bo/category/index.html.twig', 
        ['categorieForm' => $formCategory->createView(),
        'image' => $image,
        'isEdit' => $isEdit] );

    }

   
}
