<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'name',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
            ])
            ->add('dateFinalInscription', DateTimeType::class, [
                'label' => 'Date de fin d\'inscription',
            ])
            ->add('nbLimitParticipants', IntegerType::class, [
                'label' => 'nombre de place',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée',
            ])
            ->add('pictureFileName', FileType::class, [
                'mapped' => 'false',
                'required' => 'false',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
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
            'data_class' => Activity::class
        ]);
    }

}