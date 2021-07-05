<?php
namespace App\Services;

use router;
use Swift_Mailer;

use App\Services\BaseService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerService{

    private $mailer;
    private $templating;
    public function __construct(Swift_Mailer $mailer, \Twig\Environment $_templating)
    {
        $this->mailer = $mailer;
        $this->templating = $_templating;
       // parent::__construct();
    }

    public function sendMailToAdmin($_paramsMail)
{
    $mailAdmin = $_paramsMail['setTo'];
    $mailUser = $_paramsMail['setFrom'];
    $subject = $_paramsMail['subject'];
    $datas = $_paramsMail['datas'];
    $template = $_paramsMail['template'];
    $contentBody = $this->templating->render($template, $datas) ;
    
    $message = (new \Swift_Message('Nouvelle Annonce'))
            ->setFrom($mailUser)
            ->setTo($mailAdmin)
            ->setSubject($subject)
            
            ->setBody($contentBody)
        ;
 
    $this->mailer->send($message);
    

}

public function sendMailToUser($_paramsMailReturn)
{
    // dd($_paramsMailReturn);
    $to = $_paramsMailReturn['to'];
    $from = $_paramsMailReturn['from'];
    $datas = $_paramsMailReturn['datas'];
    $templates = $_paramsMailReturn['template'];
    $subject = $_paramsMailReturn['subject'];
    $contentBody = $this->templating->render($templates, $datas);

    $message = (new \Swift_Message('Message de retour de votre annonce'))
                ->setTo($to)
                ->setFrom($from)
                ->setSubject($subject)
                ->setBody($contentBody);
    $this->mailer->send($message);

}
    
}
