<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class PatientStatusImportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('patient_status_csv', Type\FileType::class, [
                'label' => 'Upload CSV File',
                'required' => true
            ])
        ;
    }
}
