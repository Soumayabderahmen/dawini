<?php

namespace App\Form;

use App\Entity\ReplaySujet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ReplaySujetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message',CKEditorType::class)
          
          ->add('images', FileType::class,[
            'label' => "Images",
            'multiple' => true,
            'mapped' => false,
            'required' => false
        ])
          ->add('Envoyer', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReplaySujet::class,
        ]);
    }
}