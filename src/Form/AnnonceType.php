<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Licence;
use App\Entity\Category;
use App\Entity\Provider;
use App\Repository\LicenceRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProviderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AnnonceType extends AbstractType
{
    protected $category;
    protected $licence;
    protected $provider;

    public function __construct(CategoryRepository $_category, 
                                LicenceRepository $_licence,
                                ProviderRepository $_provider)
    {
        $this->category = $_category;
        $this->licence = $_licence;
        $this->provider = $_provider;

    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Title')
            ->add('Description')
            ->add('Content')
            ->add('category',EntityType::class, [
                        'class' => Category::class,
                        'choice_label' => 'name',
                        'choices' => $this->category->findCategoryActive(true),
                        'attr' => [
                        'class' => 'form-control',
                             ],
                        'empty_data' => null,
                        'required' => true,
            ])
            ->add('licence', EntityType::class, [
                         'class' => Licence::class,
                         'choice_label' => 'name',
                         'choices' => $this->licence->findByStatus(),
                         'attr' => [
                            'class' => 'form-control',
                                 ],
                            'empty_data' => null,
                            'required' => true,

            ])
             ->add('provider', EntityType::class, [
                         'class' => Provider::class,
                         'choice_label' => 'name',
                         'choices' => $this->provider->getAllByStatus(),
                         'attr' => [
                            'class' => 'form-control',
                                 ],
                            'empty_data' => null,
                            'required' => true,

            ])
           // ->add('coverImage', FileType::class)
            /*
            ->add('Save', SubmitType::class,[
                'label' => 'Envoyer',
                'attr' => [
                    'required' => false,
                    'mapped' => false
                ]
              ])
              */

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
