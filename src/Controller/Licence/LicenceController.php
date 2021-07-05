<?php

namespace App\Controller\Licence;

use App\Entity\Licence;
use App\Form\LicenceType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LicenceController extends Controller{

    public function listLicence()
    {
        // $categoryService = $this->container->get('category.service');
        // $categories = $categoryService->getAlls();


        return $this->render('/bo/licence/list.html.twig');
    }

    /**
     * add new Licence function
     */
    public function new(Request $request)
    {
        $isEdit = false;
        $licenceService = $this->container->get('licence.service');
        $licence = new Licence();
        
         $formLicence = $this->createForm(LicenceType::class, $licence);
         $formLicence->handleRequest($request);

        if ($formLicence->isSubmitted() && $formLicence->isValid()){
            $alls = $request->request->All();
            $formLicence = $alls['licence'] ;
            $licenceService->checkLicence($formLicence);

            $flashMessage =  $this->addFlash('success', 'ajout de l\'annonce avec avec success');


        return $this->redirectToRoute('licence_list');
    }

       return $this->render('bo/licence/index.html.twig', [
                            'licenceForm' => $formLicence->createView(),
                            'isEdit' => $isEdit,
       ]);
    
    }

    /**
     * edit the ceteg route : licence_edit
     */
    public function editLicence(Request $request)
    {
        $licenceService = $this->container->get('licence.service');

        $isEdit = true;
        $id = $request->query->get('id');
        $categorie = $licenceService->findOneByID($id);

        $formLicence = $this->createForm(LicenceType::class, $categorie);
        $formLicence->handleRequest($request);

        if ($formLicence->isSubmitted() && $formLicence->isValid()){
            $alls = $request->request->All();
            $licences = $alls['licence'] ;
            $licences['id'] = $id;
            $licenceService->checkLicence($licences);

            $this->addFlash('success', 'Mise à Jour du licence succès');

        return $this->redirectToRoute('licence_list');

        }
        

        return $this->render('bo/licence/index.html.twig', 
        ['licenceForm' => $formLicence->createView(),
        'isEdit' => $isEdit] );

    }

   
}
