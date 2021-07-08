<?php

namespace App\Controller\Annonce;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AnnonceController extends Controller
{

    public function __construct()
    {
      

    }
   
    /**
     * Annonce new
     */
    public function new(Request $request): Response
    {
        $isEdit = false;
        $nativeFileUploader = $this->container->get('annonce.service');
        $annonce = new Annonce();
        $formAnnonce = $this->createForm(AnnonceType::class, $annonce);
        $formAnnonce->handleRequest($request);
        if ($formAnnonce->isSubmitted() && $formAnnonce->isValid()){
            $alls = $request->request->All();
            $annonces = $alls['annonce'] ;
            $nativeFileUploader->checkAnnonce($annonces);
            $flashMessage =  $this->addFlash('success', 'ajout de l\'annonce avec avec success');

        return $this->redirectToRoute('home_bo',
                             ['flashMessage' => $flashMessage,]);

         }

        return $this->render('bo/annonce/index.html.twig',
                             ['isEdit' => $isEdit,
                              'annonceForm' => $formAnnonce->createView()]);
    }


    /**
     * edit annonce
     */
    public function edit(Request $request)
    {
        $annonceService = $this->container->get('annonce.service');
        $isEdit = true;

      //  $alls= $request->request->All();
        $id = $_GET['id'];
       // $annonce =[];
        $annonceEdit = $annonceService->getById($id);
        $coverAnnonceImage = $annonceEdit->getcoverImage();
        
        $formAnnonce = $this->createForm(AnnonceType::class, $annonceEdit);
         $formAnnonce->handleRequest($request);

         if( $formAnnonce->isSubmitted() && $formAnnonce->isValid() ){
            //  $annonces = ['id' => $annonceEdit ->getId(),
            //                'Title' => $annonceEdit ->getTitle(),
            //                'Description' => $annonceEdit ->getDescription(),
            //                'Content' => $annonceEdit ->getContent(),
            //                'coverImage' => $annonceEdit ->getCoverImage(), ];
            
            $alls = $request->request->All();
            $annonce = $alls['annonce'];
            $annonce['id'] = $id;
            $annonceService->checkAnnonce($annonce);
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement succès');
            
            return $this->redirectToRoute('home_bo');
            $this->addFlash('success', 'Mise à ajour de l\'annonce avec succès');
         }
        //$annonceService->checkAnnonce(['id' => $id]);

        return $this->render('bo/annonce/index.html.twig',
                             ['isEdit' => $isEdit,
                             'coverAnnonceImage' => $coverAnnonceImage,
                             'annonceForm' => $formAnnonce->createView()]);


    }

    public function list()
    {
        
        return $this->render('bo/annonce/list.html.twig');

    }

    /**
     *  export the annonce datas to CSV files 
     * route : annonce_export_csv
     */
    public function exportAnnonceToCsv()
    {
        // $baseurl = $request->getScheme() .'://'. $request->getHttpHost().$request->getBasePath();

        // $response = new StreamedResponse();


        
        // $response->setCallback( function() use(&$baseurl) {
         $annonceService = $this->container->get('annonce.service');

        //     $handle = fopen('php://output', 'w+');
        //      // Ajout UTF8
        //      fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        //      $delimiter = ";";
        //      // Add the header of the CSV file
        //     fputcsv($handle, ['Code ID', 'Titre', 'Description', 'Contenu', 'Catégorie', 'Licence'], $delimiter);

            $allDatas = $annonceService->takeAllDatas();
        //     $title = $allDatas['title'];
        //     $description = $allDatas['description'];
        //     $content = $allDatas['content'];
        //     $category = $allDatas['category'];
        //     $licence = $allDatas['licence'];
        //     $idAnnonce = $allDatas['id'];


        //     fputcsv(
        //         $handle, // The file pointer 
        //         //[$idAnnonce, $title, $description, $content, $category,$licence], // The fields
        //        [$idAnnonce,52,5,56,5,5],
        //         $delimiter // The delimiter
        //     );
        //     fclose($handle);
        // });

        // $datenow = (new \DateTime())->format('dmYHis');
        // $filename = "export_liste_bdd_".$datenow.".csv";
        // $response->headers->set('Content-Type', 'application/force-download; charset=utf-8');
        // $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);


        return $allDatas;
    }



   
}
