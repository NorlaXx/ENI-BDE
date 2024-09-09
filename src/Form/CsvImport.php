<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class CsvImport extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('csvFile', FileType::class, [
                'label' => 'Import CSV',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV document',
                    ])
                ],
            ]);
    }

}