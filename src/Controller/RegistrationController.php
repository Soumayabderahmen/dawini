<?php

namespace App\Controller;

use App\Entity\Patient;
// use App\Entity\User;
use App\Entity\Medecin;
use App\Form\PersonnelRegisterType;
use App\Form\Register1Type;

use App\Form\RegistrationMedecinType;
use App\Form\PatientRegistrationType;
use App\Form\VerificationType;
use App\Repository\MedecinRepository;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
// use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, SluggerInterface $slugger ,UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Patient();
       
       
        $form = $this->createForm(PatientRegistrationType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()&&$user->getConfirmPassword()) {

                $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                 
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImage($newFilename);
            }
            $user->setPassword($userPasswordHasher->hashPassword($user,$form->get('password')->getData() ));
            $user->setConfirmPassword($userPasswordHasher->hashPassword($user,$form->get('confirm_password')->getData()));
            $user->eraseCredentials();
            }
            $roles[]='ROLE_PATIENT';
            $user->setRoles($roles);
            $userRepository->save($user, true);
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_login', [$userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
        )], Response::HTTP_SEE_OTHER);
            }
           
        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

        // return $this->redirectToRoute('app_login');
    }

    #[Route('/inscription/medecin', name: 'app_inscription_medecin')]
    public function registerMedecin(Request $request, SluggerInterface $slugger ,MedecinRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
      
        $user = new Medecin();
       
       
        $form = $this->createForm(RegistrationMedecinType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()&&$user->getConfirmPassword()) {

                $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                 
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImage($newFilename);
            }
            $user->setPassword($userPasswordHasher->hashPassword($user,$form->get('password')->getData() ));
            $user->setConfirmPassword($userPasswordHasher->hashPassword($user,$form->get('confirm_password')->getData()));
            $user->eraseCredentials();
            }
            $user->setEnabled(false); // Etat désactivé par défaut
            $roles[]='ROLE_MEDECIN';// Rôle Médecin
            $user->setRoles($roles);
            $userRepository->save($user, true);
            $this->addFlash('success', 'Votre compte a été créé avec succès. Veuillez attendre que l\'administrateur active votre compte.');

            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_login', [$userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
        )], Response::HTTP_SEE_OTHER);
            }
           
        
        return $this->render('registration/doctor-register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);


        

        // return $this->redirectToRoute('app_login');
    }





}
