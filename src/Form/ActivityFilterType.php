<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\User;
use App\Model\ActivityFilter;
use PHPUnit\Util\Filter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus :',
                'choice_label' => 'Name',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie :',
                'required' => false,
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Début',
                'required' => false,
            ])
            ->add('dateMax', DateType::class, [
            'label' => 'Fin',
                'required' => false,
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Je suis l\'organisateur',
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Je suis inscrit',
                'required' => false,
            ])
            ->add('notInscrit', CheckboxType::class, [
                'label' => 'Je ne suis pas inscrit',
                'required' => false,
            ])
            ->add('finis', CheckboxType::class, [
                'label' => 'Les sorties passées',
                'required' => false,
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActivityFilter::class,
        ]);
    }

}