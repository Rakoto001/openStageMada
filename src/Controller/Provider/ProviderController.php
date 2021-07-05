<?php

namespace App\Controller\Provider;

use App\Entity\Provider;
use App\Form\ProviderType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProviderController extends Controller{

    /**
     * list all providers
     */
    public function listProvider()
    {

        return $this->render('/bo/provider/list.html.twig');
    }

    /**
     * add new provider
     */
    public function new(Request $request)
    {
        $isEdit = false;
        $providerService = $this->container->get('provider.service');
        $provider        = new Provider();
        $formProvider    = $this->createForm(ProviderType::class, $provider);
        $formProvider->handleRequest($request);
        if ($formProvider->isSubmitted() && $formProvider->isValid()){
            $alls = $request->request->All();
            $providers = $alls['provider'] ;
            $providerService->checkProvider($providers);
            $flashMessage =  $this->addFlash('success', 'ajout de l\'annonce avec avec success');

         return $this->redirectToRoute('provider_list');
        }

       return $this->render('bo/provider/index.html.twig', 
                            [
                                'providerForm' => $formProvider->createView(),
                                'isEdit' => $isEdit
                            ] 
                        );
    
    }

    /**
     * edit the provider
     * route : provider_edit
     */
    public function editProvider(Request $request)
    {
        $providerService = $this->container->get('provider.service');
        $isEdit = true;
        $id = $request->query->get('id');
        $provider = $providerService->getById($id);
        $image = $provider->getImage();
        $formProvider = $this->createForm(ProviderType::class, $provider);
        $formProvider->handleRequest($request);
        if ($formProvider->isSubmitted() && $formProvider->isValid()){
            
            $alls      = $request->request->All();
            $providers = $alls['provider'] ;
            $providers['id'] = $id;
            $providerService->checkProvider($providers);
            $this->addFlash('success', 'Mise à Jour du provider succès');

            return $this->redirectToRoute('provider_list');
        }
        
        return $this->render('bo/provider/index.html.twig', 
                                                        [
                                                            'providerForm' => $formProvider->createView(),
                                                            'image' => $image,
                                                            'isEdit' => $isEdit
                                                        ] 
                                                    );

    }

   
}
