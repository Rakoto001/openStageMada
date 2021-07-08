<?php

namespace App\Controller\Stagiaire;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StagiaireController extends Controller
{

    public function newStagiaire(Request $request)
    {
        $stagiaireService = $this->container->get('stagiaire.service');

        $isEdit = false; 
        $stagiaire = new Stagiaire();
        $stagiaireForm = $this->createForm(StagiaireType::class, $stagiaire);
        $stagiaireForm->handleRequest($request);
        
        
        if ($stagiaireForm->isSubmitted() && $stagiaireForm->isValid()){
            $alls = $request->request->All();
            $params = $alls['stagiaire'];

            try {
                $stagiaireInformations = $stagiaireService->stagiaireRegistration($params);
            } catch(\Doctrine\DBAL\DBALException $e)
            {
              //  dd($e);
                //gestion erreur quand le email ou username est déjà utilisé
                $this->addFlash('error', 'une erreur s\'est produite lors de la création de votre compte, veuillez bien suivre les instructions et réessayer a nouveau' );
               
                return $this->redirectToRoute('user_register');
            }
            $this->addFlash('success', 'Votre profil a été enregistré avec succèes' );

            return $this->redirectToRoute('home_bo');


            
        }

        return $this->render('email/confirm-pdf.html.twig');

        // return $this->render('bo/stagiaire/index.html.twig', [
        //     'stagiaireForm' => $stagiaireForm->createView(),
        //     'isEdit' => $isEdit,

        // ]);
    }

     /**
     * Modifier le status
     * @Route("/not/main", name="not-main", options={"expose"=true})
     * @param Article $article
     * @return JsonResponse|RedirectResponse
     */
    public function notMain()
    {
        return $this->render('bo/not.html.twig');
        
    }
}
