<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Licence;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\LicenceRepository;

class AnnonceType extends AbstractType
{
    protected $category;
    protected $licenec;
    public function __construct(CategoryRepository $_category, LicenceRepository $_licence)
    {
        $this->category = $_category;
        $this->licence = $_licence;

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
