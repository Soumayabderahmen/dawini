<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('nom')
        ->add('prenom')
        ->add('email')
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
        
        
        
        // ->add('images', FileType::class,[
        //     'label' => false,
        //     'multiple' => true,
        //     'mapped' => false,
        //     'required' => false,
        //     'label' => 'Images'
        // ])
       
            ->add('email')
           
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirm_password',PasswordType::class)
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('Enregistrer', SubmitType::class)
        ;
    }
  


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
