<?php

namespace App\Form;

use App\Entity\Diagnostique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DiagnostiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date',DateType::class,[
            'widget'=>'single_text',
            'label'=>'date'
        ])
            ->add('symptome')
            ->add('resultat_test')
            ->add('diag_final')
            ->add('Enregistrer', SubmitType::class)
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diagnostique::class,
        ]);
    }
}
