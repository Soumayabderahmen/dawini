<?php

namespace App\Controller;

use App\Entity\Patient;
// use App\Entity\User;
use App\Entity\Medecin;

// use App\Service\Mailer;
use App\Form\RegistrationMedecinType;
use App\Form\PatientRegistrationType;
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
use App\Service\Mailer;
use Symfony\Component\String\Slugger\SluggerInterface;


// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Symfony\Component\Serializer\SerializerInterface;
// use Symfony\Component\Mailer\MailerInterface;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    
    private $mailer;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct( Mailer $mailer, UserRepository $userRepository)
    {
       
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

   

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
            
            $roles[]='ROLE_MEDECIN';// Rôle Médecin
            $user->setRoles($roles);
            $user->setToken($this->generateToken());
            $userRepository->save($user, true);
            $this->mailer->sendEmail($user->getEmail(), $user->getToken());
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
     /**
     * @Route("/confirmer-mon-compte/{token}", name="confirm_account")
     * @param string $token
     */
    public function confirmAccount(string $token)
    {
        $user = $this->userRepository->findOneBy(["token" => $token]);
        if($user) {
            $user->setToken(null);
            $user->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Compte actif !");
            return $this->redirectToRoute("home");
        } else {
            $this->addFlash("error", "Ce compte n'exsite pas !");
            return $this->redirectToRoute('home');
        }
    }
    
    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }



}
