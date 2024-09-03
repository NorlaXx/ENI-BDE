<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ActivityFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie',
                'required' => false,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'Name',
                'required' => false,
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Date min',
                'required' => false,
            ])
            ->add('dateMax', DateType::class, [
            'label' => 'Date max',
                'required' => false,
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Organisateur',
                'required' => false,
        ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Inscrit',
                'required' => false,
            ])
            ->add('finis', CheckboxType::class, [
                'label' => 'Finis',
                'required' => false,
            ]);
    }

}