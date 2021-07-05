<?php

namespace App\Form;

use App\Entity\Stagiaire;
use App\Services\StagiaireService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StagiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('FullName')
            ->add('Sexe', ChoiceType::class,[
                'label' => false,
                'choices' => array_flip(StagiaireService::STAGIAIRE_GENDER),
                'mapped' => false,
            ])
            ->add('Grade')
            ->add('Experience')
            ->add('Contact')
            //->add('CV')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
