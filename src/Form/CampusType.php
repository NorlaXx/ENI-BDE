<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CampusType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Campus',
                'attr' => array(
                    'placeholder' => 'Nom du Campus'
                )
            ])
            ->add('nblimitPlaces', IntegerType::class, [
                'label' => 'Nombre de places totales',
                'attr' => array(
                    'placeholder' => 'Nombre de places totales'
                )
            ])
            ->add('postalCode', IntegerType::class, [
                'label' => 'Code Postale',
                'attr' => array(
                    'placeholder' => 'Code Postale'
                )
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => array(
                    'placeholder' => 'Adresse'
                )
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => array(
                    'placeholder' => 'Ville'
                )
            ])
            ->add('fileName', FileType::class, [
                'label' => 'Upload une image',
                'label_attr' => ['class' => 'file-label'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Fichiers accepetés: jpeg, png',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campus::class
        ]);
    }

}