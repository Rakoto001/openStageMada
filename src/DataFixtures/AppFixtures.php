<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Licence;
use App\Entity\Postulate;
use App\Entity\Provider;
use App\Entity\Stagiaire;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $_encoder) {
        $this->encoder = $_encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR:fr');

        
        // for user
        for($fakeUser = 1; $fakeUser<10; $fakeUser++){
            $user = new User;

            $user->setUsername('user'.$fakeUser)
                 ->setLastname('user'.$fakeUser)
                 ->setFirstname('user'.$fakeUser)
                 ->setEmail('user'.$fakeUser.'@gmail.com')
                 ->setRoles(['ROLE_USER'])
                 ->setPassword($this->encoder->encodePassWord($user, '123456'))
                 ->setStatus(mt_rand(0,1))
                 ->setGender('F')
                 ->setAvatar('https://randomuser.me/api/portraits/');
                $manager->persist($user);
            $users[] = $user;

        }
        


        //for category

        for($fakerCategory=1; $fakerCategory<10; $fakerCategory++){
            $category = new Category;

            $category->setName('category'.$fakerCategory)
                     ->setImage('https://randomuser.me/api/portraits/')
                     ->setStatus(mt_rand(0,1))
                     ->setContent($faker->sentence());
            $manager->persist($category);
            $categories[] = $category;
        }


        //for licence
        for($fakeLicence=1; $fakeLicence<10; $fakeLicence++){
            // dump($categories[mt_rand(0,9)]);
            $licence = new Licence;
            $licence->setName('licence'.$fakeLicence)
                    ->setContent($faker->sentence())
                    ->setStatus(mt_rand(0,1))
                    ->setUrlOfficial('www.licence'.$fakeLicence.'.com');
            $manager->persist($licence);
            $licences[] = $licence;
        }

        //for provider
        for($fakeProvider=1; $fakeProvider<10; $fakeProvider++){
            $provider = new Provider;
            $provider->setName('provider'.$fakeProvider)
                     ->setImage('https://randomuser.me/api/portraits/')

                     ->setEmail('emailProvider'.$fakeProvider.'@gmail.com')
                     ->setStatus(mt_rand(0,1));
            $manager->persist($provider);
            $providers[] = $provider;
        }

        //for annonce
        for($fakeAnnonce=1; $fakeAnnonce<20; $fakeAnnonce++){
            $annonce = new Annonce;
            $annonce->setTitle($faker->sentence())
                    ->setDescription($faker->sentence())
                    ->setContent($faker->sentence())
                    ->setCoverImage('https://randomuser.me/api/portraits/')

                    ->setCategory($categories[mt_rand(0,8)])
                    ->setCreatedBy($users[mt_rand(0,8)])
                    ->setLicence($licences[mt_rand(0,8)])
                    ->setProvider($providers[mt_rand(0,8)]);
            $manager->persist($annonce);
            $annonces[] = $annonce;
        }

        //for stagiaire
        for($fakeStagiaire=1; $fakeStagiaire<10; $fakeStagiaire++){
            $stagiaire = new Stagiaire;
            $stagiaire->setFullName('stagiaire'.$fakeStagiaire)
                      ->setSexe('M')
                      ->setGrade($faker->sentence())
                      ->setExperience('pas d\'expérience')
                      ->setContact(mt_rand(25,89))
                      ->setCV($faker->sentence());
            $manager->persist($stagiaire);

            $stagiaires[] = $stagiaire;
        }

        // for postulate
        for($fakePostulate=1; $fakePostulate<10; $fakePostulate++){
            $postulate = new Postulate;
            $postulate->setStagiaire($stagiaires[mt_rand(0,8)])
                      ->setAnnonce($annonces[mt_rand(0,8)]);
            $manager->persist($postulate);                      
        }




        $manager->flush();
    }
}
