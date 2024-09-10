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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ActivityType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie',
                'attr' => array(
                    'placeholder' => 'Nom de la sortie'
                )
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'name',
                'choice_attr' => function(Lieu $lieu) {
                    return ['data-ville' => $lieu->getCity()];
                },
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => array(
                    'placeholder' => 'Description'
                )
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date de début',
                'required' => false,
            ])
            ->add('registrationDateLimit', DateTimeType::class, [
                'label' => 'Date de fin d\'inscription',
                'required' => false,
            ])
            ->add('nbLimitParticipants', IntegerType::class, [
                'label' => 'nombre de place',
                'attr' => array(
                    'placeholder' => 'Nombre de place'
                )
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée',
                'attr' => array(
                    'placeholder' => 'Durée'
                )
            ])
            ->add('share', SubmitType::class, ['label' => 'Enregistrer'])

            ->add('save', SubmitType::class, ['label' => 'Publier'])

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
            'data_class' => Activity::class
        ]);
    }

}