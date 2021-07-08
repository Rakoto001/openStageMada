<?php

namespace App\Services;

use router;
use Swift_Mailer;

use Knp\Snappy\Pdf;
use Twig\Environment;
use App\Entity\Annonce;
use App\Services\BaseService ;
use Doctrine\ORM\Mapping\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class AnnonceService extends BaseService

{
    protected $manager;
    protected $service;
    protected $category;
    protected $mailer;
    protected $twig;
    protected $pdf;
    public const LICENCE_NULL = 'Cette Annonce ne dispose d\'aucune licence ';
   // protected $request;
    /**
     * constructor
     */
    public function __construct(EntityManagerInterface $_manager,
                                ContainerInterface $_container, 
                                Swift_Mailer $_mailer,
                                Environment $_twig,
                                Pdf $_pdf   ){

        $this->manager = $_manager;
        $this->container = $_container;
        $this->mailer = $_mailer;
        $this->twig = $_twig;
        $this->pdf = $_pdf;
    }

    public function getRepository()
    {

        return $this->manager->getRepository(Annonce::class);
    }

    public function checkAnnonce($_annonces = [])
    {
        $isNew = false;
        $nativeService = $this->container->get('native.upload.service');
        $categoryService = $this->container->get('category.service');
        $licenceService = $this->container->get('licence.service');
        $mailerService = $this->container->get('mailer.service');
        $providerService = $this->container->get('provider.service');
        $id = isset($_annonces['id']) ? $_annonces['id'] : '0';
        $title = $_annonces['Title'];
        $description = $_annonces['Description'];
        $content = $_annonces['Content'];
        $idCategory = $_annonces['category'];
        $idLicence = $_annonces['licence'];
        $idProvider = $_annonces['provider'];
        //find obj by id
        $category = $categoryService->getById($idCategory);
        $licence = $licenceService->getById($idLicence);
        $provider = $providerService->getById($idProvider);
        // dd($provider);
        if(empty($_annonces['id'])){
            $annonce = new Annonce();
            $curentUser =  $this->container->get('security.token_storage')->getToken()->getUser();
            $mailUser = 'rakotoarisondan@gmail.com';
            // $mailUser = $curentUser->getEmail();
            $mailAdmin = 'danielorak1@gmail.com';
            //convert html to pdf
            // $html = $this->twig->render('email/confirm-pdf.html.twig');
            // $pdf  = $this->pdf->getOutputFromHtml($html);
           
            //envoi mail vers l'admin sur un nouvel annonce
            $emailParameters = [
                                    'setTo' => $mailAdmin,
                                    'setFrom' => $mailUser,
                                    'subject' => 'Nouvelle Annonce par un utilisateur',
                                    'template' => 'email/confirm.html.twig',
                                    'datas' => ['title' => $title,
                                                'description' => $description
                                               ],
                                    //  'pdf'  => $pdf
                                    
            ];
            // dd($emailParameters);

            $mailerService->sendMailToAdmin($emailParameters);

            $paramsMailReturn = ['to'    => $mailUser,
                                 'from'  => $mailAdmin ,
                                 'subject' => 'Message retour concernant votre annonce de stage',
                                 'template' => 'email/retour.html.twig',
                                 'datas' => ['userName' => $curentUser->getUsername(),
                                             'mailAdmin' => $mailAdmin 
                                             ]
                                  ];
            

            $mailerService->sendMailToUser($paramsMailReturn);

            $annonce->setCreatedBy($curentUser);

            $isNew = true;
        }else{

            $annonce = $this->getById($id);
           
        }
        $annonce->setTitle($title);
        $annonce->setDescription($description);
        $annonce->setContent($content);
        $annonce->setCategory($category);
        $annonce->setLicence($licence);
        $annonce->setProvider($provider);
       
        if(isset($_FILES['coverImage']) && !empty($_FILES['coverImage']['name']) > 0){
            $imageFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_annonce_cover');
            $nativeService->makePath($imageFullPath);
            $filename = $nativeService->upload($imageFullPath,'coverImage');
            $annonce->setCoverImage($filename);
            $this->save($annonce) ;
        }
        $this->manager->persist($annonce);
        $this->manager->flush();

    }
 
    /**
     * list all annonces 
     */
    public function annonceList($_params)
    {
        $annonces = $this->find($_params, ['offset' => $_params['offset'],'limit' => $_params['length']])->getResult(); 
        $totAnnonces = count($annonces);
        $result = [];

        foreach($annonces as $annonce){

            $imageFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads';
            //action delete or edit
            $annonceAction  = '<a href="'.$this->generateUrl($annonce).'" > <i class="fa fa-edit"></i> </a>';
            $annonceAction  .= '<a href="javascript:;" ajax-url="'.$this->generateUrl($annonce, 'delete').'" data-id="'.$annonce->getId().'" class="delete-annonce"> <i class="fa fa-trash-o"></i> </a>';
        
            //catégorie
            if( !empty($annonce->getCategory()) ){
             $categoryName = $annonce->getCategory()->getName();

            }else{
                $categoryName = 'Vide';
            }
            //image
            if(!empty($annonce->getCoverImage())){
            $coverImage = '<img src="/uploads/cover/'.$annonce->getCoverImage().'" width="30"/>';
                
            }else{
            $coverImage = '<img src="/uploads/default/coverDefault.png" width="30"/>';

            }
            if($_params['page'] == 'annonce'){
                //dd($annonce->getId());
                $rows =  array( $coverImage,
                                $categoryName,
                                $annonce->getTitle(), 
                                $annonce->getDescription(),
                                $annonceAction
                                //'<a href="'.$this->generateUrl($annonce, 'delete').'"> <i class="fa fa-trash-o"></i> </a>'
                        );

            }
    

            $result[] = $rows;
    
    }
    //  $dataResults = [$titre, $description];
        return ['datas' => $result, 'count' => $totAnnonces];
    }

   

     /**
      * function that generate URL for delete or edit
      */
     public function generateUrl($_annonce, $action = null)
     {
         $url = $this->container->get('router');
         if($action =='delete'){

           $result = $url->generate('annonce_ajax_delete', ['id' => $_annonce->getId()]);

         }else{

            $result = $url->generate('annonce_edit', ['id' => $_annonce->getId()]);
}
     return $result;
     }

      /**
      * find annoce objct 
      */
      public function findAnnonce($id)
      {
 
          $annonce = $this->getById(['id'=>$id]);
 
          return $annonce;
 
      }

       
     /**
      * delete annonce object and image
      */
     public function delete($_object)
     {
        //  dd($_object->getCoverImage());
        $imageAnnonceFullPath = $this->container->get('kernel')->getRootDir() . '/../public/uploads'."/".$this->container->getParameter('uploads_annonce_cover')."/".$_object->getCoverImage();
        if( file_exists($imageAnnonceFullPath) ){
            unlink($imageAnnonceFullPath);
        }  
        
         return $this->removeData($_object);
     }
 

    /**
     * function find
     */
    public function find($_params, $_offset = array(), $_limit = array())
    {
        $query = $this->getRepository()->createQuery($_params);
        $query = $this->getRepository()->findByFilter($_params, $query);
        //findAlls
        $query = $this->getRepository()->findAlls($query);
        $query = $this->getRepository()->addLimit($_params, $query);
        $query = $this->getRepository()->orderBy($query, $_limit);

    return $query->getQuery();

    }

    /**
     *  take the datas from the annonce
     */
    public function takeAllDatas()
    {
       $request = new Request();
       $baseurl = $request->getScheme() .'://'. $request->getHttpHost().$request->getBasePath();

       $response = new StreamedResponse();
       //$annonceDatas = [];

       $response->setCallback(function() use(&$baseurl) {
       $annonces =  $this->getRepository()->findAll();

        $handle = fopen('php://output', 'w+');
            // Ajout UTF8
        fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        $delimiter = ";";
              // Add the header of the CSV file
              fputcsv($handle, ['Code ID', 'Titre', 'Description', 'Contenu', 'Catégorie', 'Licence'], $delimiter);

       foreach( $annonces as $annonce){

        $title = $annonce->getTitle();
        $description = $annonce->getDescription();
        $content = $annonce->getContent();
        $categorie = $annonce->getCategory()->getName();
        $licence = $annonce->getLicence();
        $idAnnonce = $annonce->getId();

        if($licence == null){
            $licence = self::LICENCE_NULL;
        }else{
            $licence = $licence->getName();
        }
        fputcsv(
            $handle, // The file pointer 
            [$idAnnonce,$title,$description,$content,$categorie,$licence],
            $delimiter // The delimiter
        );

    }
        fclose($handle);
    });

        $datenow = (new \DateTime())->format('dmYHis');
        $filename = "export_liste_bdd_".$datenow.".csv";
        $response->headers->set('Content-Type', 'application/force-download; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);

    
  
   return $response;
    }

    /**
     * find all announce by the category ID
     */
    public function listAnnonceByCategory($_id)
    {
        $annonces = $this->getRepository()->findAnnonceByCateg($_id);

        return $annonces;


    }


     /**
     * front- list all the annonces
     */
    public function listAllAnonce($_more)
    {
        $listCategories = $this->getRepository()->findByDate($_more);

        return $listCategories;

    }

      /**
     * find all announce by the provider ID
     */
    public function listAnonceByProvider($_id)
    {

        $annonces = $this->getRepository()->findAnnonceByProvider($_id);

        return $annonces;


    }
    




    
}