<?php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Services\UserService ;


class GenerateAdminCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'admin:create';
    private $container;
    private $manager;

    public function __construct(ContainerInterface $_container, EntityManagerInterface $_manager)
    {
        parent::__construct();

        $this->container = $_container;
        $this->manager = $_manager;

    }

    protected function configure(): void
    {
        $this->setDescription('Creates a new user.')
             ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'ADMIN CCREATOR',
            '============',
            '',
        ]);
        $begin = time();
        $output->write('Begin script '  .date('d/m/Y H:i:s'));
        $userService = $this->container->get('user.service');
        $userService->defaultUserRegistration();

        $output->write('End script ');
        $output->write('SUCCESSFULLY DONE');


        return 0;

      
    }
}