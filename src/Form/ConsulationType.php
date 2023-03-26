<?php

namespace App\Form;

use App\Entity\Consulation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class ConsulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('heuredebut',null, [ 
            'widget' => 'single_text',
        ])
        ->add('heurefin',null, [ 
            'widget' => 'single_text',
        ])
        
        ->add('Enregistrer', SubmitType::class)
        
    ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consulation::class,
        ]);
    }
}
