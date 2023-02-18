<?php

namespace App\Form;

use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
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
        ->add('fixe')
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
            ->add('titre', ChoiceType::class, [
                'choices'  => [
                    'Medecin' => 'Medecin',
                    'Professeur' => 'Professeur',
                ],
            ])


      
       
        
        ->add('email')
     
        ->add('password',PasswordType::class)
        ->add('confirm_password',PasswordType::class)
        ->add('tarif')
        ->add('specialites')
        ->add('cnam' ,ChoiceType::class, [
            'choices'  => [
                'Oui' => 'Oui',
                'Non' => 'Non',
            ],
        ])
        ->add('adresse',TextareaType::class, [
            'attr' => ['class' => 'tinymce'],
        ])
 
        ->add('adresse_cabinet',TextareaType::class, [
            'attr' => ['class' => 'tinymce'],
        ])
     
        ->add('diplome_formation',TextareaType::class, [
            'attr' => ['class' => 'tinymce'],
        ])
        // ->add('imagesCabinet', FileType::class,[
        //     'label' => false,
        //     'multiple' => true,
        //     'mapped' => false,
        //     'required' => false,
        //     'label' => 'Images Cabinet'
        // ])
        
       
        // ->add('images', FileType::class, [
        //     'label' => false,
        //     'multiple' => true,
        //     'mapped' => false,
        //     'required' => false,
            // 'constraints' => [
            //     new All(
            //         new Image([
            //             'maxWidth' => 1280,
            //             'maxWidthMessage' => 'L\'image doit faire {{ max_width }} pixels de large au maximum'
            //         ]
                    
            //         )
            //     )
            // ]
        // ])
        
        ->add('photo', FileType::class, [
            'label' => 'Votre image de profil (Des fichiers images uniquement)',
            // unmapped means that this field is not associated to any entity property
            'mapped' => false,
            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,
            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid Image',
                ])
            ],
        ])


          
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medecin::class,
        ]);
    }
}
