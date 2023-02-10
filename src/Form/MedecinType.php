<?php

namespace App\Form;

use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class MedecinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('nom')
            ->add('prenom')
            ->add('cin')
            ->add('sexe', ChoiceType::class, [
                'choices'  => [
                    'Femme' => 'Femme',
                    'Homme' => 'Homme',
                    'Autre' => 'Autre',
                ],
            ])
            ->add('telephone')
            ->add('gouvernorat', ChoiceType::class, [
                'choices' =>[
                    'Ariana' => 'Ariana',
                    'Beja' => 'Beja',
                    'Ben Arous' => 'Ben_Arous',
                    'Bizerte' => 'Bizerte',
                    'Gabes' => 'Gabes',
                    'Gafsa' => 'Gafsa',
                    'Jendouba' => 'Jendouba',
                    'kairouan' => 'kairouan',
                    'Kasserine' => 'Kasserine',
                    'Kebili' => 'Kebili',
                    'Kef' => 'Kef',
                    'Mahdia' => 'Mahdia',
                    'Manouba' => 'Manouba',
                    'Médnine' => 'Médnine',
                    'Monastir' => 'Monastir',
                    'Nabeul' => 'Nabeul',
                    'Sfax' => 'Sfax',
                    'Sidi Bouzid' => 'Sidi_Bouzid',
                    'Siliana' => 'Siliana',
                    'Soussa' => 'Soussa',
                    'Tataouine' => 'Tataouine',
                    'Tozeur' => 'Tozeur',
                    'Tunis' => 'Tunis',
                    'Zaghouan' => 'Zaghouan',
                ],
                ])
       
            ->add('adresse',TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ])
     
           
            ->add('adresse')
            ->add('email')
         
            ->add('password',PasswordType::class)
            ->add('confirm_password',PasswordType::class)
            ->add('titre', ChoiceType::class, [
                'choices'  => [
                    'Medecin' => 'Medecin',
                    'Professeur' => 'Professeur',
                ],
            ])
            ->add('adresse_cabinet',TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('fixe')
            ->add('diplome_formation',TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('tarif')
            ->add('cnam')
            ->add('horraire_ouverture')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medecin::class,
        ]);
    }
}