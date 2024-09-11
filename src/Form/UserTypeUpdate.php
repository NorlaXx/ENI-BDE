<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use phpDocumentor\Reflection\PseudoTypes\StringValue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserTypeUpdate extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phone_number', TextType::class, [
                'label' => 'Phone Number',
                'attr' => ['placeholder' => 'Numéro de téléphone'],
                'required' => true,
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr' => ['placeholder' => 'Pseudo'],
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom'],
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom'],
                'required' => true,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un campus',
                'label' => 'Campus',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Email'],
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'password',
                'mapped' => false,
                'attr' => ['placeholder' => 'password',
                    'type' => 'password',
                    ],
                'required' => false,
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'label' => '',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'confirmer son mot de passe',
                    'type' => 'password',
                ],
                'required' => false,
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Profile Picture (PDF, PNG, JPG file)',
                'label_attr' => ['class' => 'file-label'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF, JPG, or PNG document',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Associate this form with the User entity
            'csrf_protection' => false,
        ]);

    }

}