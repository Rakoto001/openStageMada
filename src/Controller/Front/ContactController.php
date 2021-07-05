<?php
namespace App\Controller\Front;
//après installation de TWIG on doit mettre extends AbstractController
use App\Form\ContactType;
//pour obtenir le nom et les params des toutes
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends Controller{

	// public function __construct(Type $var = null)
	// {
	// 	# code...
	// }

	/**
	 * contact the admin, send mail to the admin
	 */
	public function contactAdmin(Request $request)
	{
        $mailerService = $this->container->get('mailer.service');

		$formContact = $this->createForm(ContactType::class);

		$formContact->handleRequest($request);

        if ($formContact->isSubmitted() && $formContact->isValid()){

			$alls = $request->request->All();
			$contacts =$alls['contact'];
			$name = $contacts['name'];
			$from = $contacts['email'];
			$contact = $contacts['contact'];
			$theme = $contacts['subject'];
			$message = $contacts['message'];
			
			$paramsMailToUser = ['name' => $name,
							'to'   => 'rakotoarisondan@gmail.com',
							'datas' => ['userName' => $name, 
										'link' 	   => 'http://madastage.loc/admin/annonce/new',
										],
							'subject' => $theme,
							'template' => 'email/send-to-user.html.twig',
							'from' => $from,
			];
			// dd($paramsMail);
			$mailerService->sendMailToUser($paramsMailToUser);

			// envoi vers admin
			$paramsMailToAdmin = ['name'    => $name,
								 'setTo'    => 'rakotoarisondan@gmail.com',
								 'datas'    => [ 'subject'  => $theme,
												 'message'  => $message,
												 'mailUser' => $from],
								 'subject'  => $theme,
								 'template' => 'email/send-to-admin.html.twig',
								 'setFrom'  => $from,
			];
			$mailerService->sendMailToAdmin($paramsMailToAdmin);

           $this->addFlash('success', 'Annonce bien envoyée vers l\'administrateur ');

        	return $this->redirectToRoute('front_annonce_contact',);

         }

		
	return $this->render('fo/contact/contact-admin.html.twig', [
																'contactForm' => $formContact->createView(),
																]);
	}




}


?>