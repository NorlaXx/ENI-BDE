<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LieuType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de du lieu',
                'attr' => array(
                    'placeholder' => 'Nom du lieu'
                )
            ])
            ->add('city', TextType::class, [
                'label' => 'city',
                'attr' => array(
                    'placeholder' => 'Ville'
                )
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'code postal',
                'attr' => array(
                    'placeholder' => 'Code postal'
                )
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => array(
                    'placeholder' => 'Adresse'
                )
            ])
            ->add('fileName', FileType::class, [
                'label' => 'Image (jpeg, png)',
                'label_attr' => ['class' => 'file-label'],
                'mapped' => false,  
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Fichiers accepetés: jpeg, png',
                    ])
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class
        ]);
    }

}