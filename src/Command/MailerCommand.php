<?php

namespace App\Command;
use Swift_Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailerCommand extends Command
{
   // protected static $defaultName = 'send:email';
    private $mailer;
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setName('send:mail')
            ->setDescription('user', null, InputOption::VALUE_REQUIRED , 'email de user', null)
            ->setDescription('society', null, InputOption::VALUE_REQUIRED , 'society de user', null)
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Command Send Self Email',
            '============'
        ]);
        
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('symfony9494@gmail.com')
            ->setTo('rakotoarisondan@gmail.com')
            ->setBody("Envoi Mail by Dan", 'text/html')
        ;


        $this->mailer->send($message);
        $output->writeln('Successful you send a self email');
    }
}