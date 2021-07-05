<?php

namespace App\Form;

use App\Entity\User;
use App\Services\UserService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('lastname')
            ->add('firstname')
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                          'label' => false,
                          'choices'  => array_flip(UserService::ROLES),
                          'mapped' => false,

            ])
            //->add('password')
            ->add('status', ChoiceType::class,[
                'label' => false,
                'choices' => array_flip(userservice::USER_STATUS),
                'mapped' => false,

            ])
            ->add('gender', ChoiceType::class,[
                'label' => false,
                'choices' => array_flip(userservice::USER_GENDER),
                'mapped' => false,
            ])
            // ->add('created')
            // ->add('updated')
        //    ->add('avatar',FileType::class,[
        //     'label'=> false,
        //     "multiple" =>true,
        //     "mapped" => false,
        //     'required' =>false,
        //     'attr'=>[
        //         'accept'=>'image/*',
        //     ],
        // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
