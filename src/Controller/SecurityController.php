<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/auth', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
      
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout():Response
    {

       throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        return $this->redirectToRoute('app_login');

    }
    #[Route(path: '/forgot', name: 'forgot')]
    public function forgotPassword(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator,ManagerRegistry $doctrine)
    {


        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();//


            $user = $userRepository->findOneBy(['email' => $donnees]);
            if (!$user) {
                $this->addFlash('danger', 'cette adresse n\'existe pas');
                return $this->redirectToRoute('forgot');

            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();


            } catch (\Exception $exception) {
                $this->addFlash('warning', 'une erreur est survenue :' . $exception->getMessage());
                return $this->redirectToRoute('app_login');


            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            //BUNDLE MAILER
            $message = (new \Swift_Message('Mot de password oublié'))
                ->setFrom('soumaya.abderahmen@esprit.tn')
                ->setTo($user->getEmail())
                ->setBody("<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :" . $url,
                    "text/html");

            //send mail
            $mailer->send($message);
            $this->addFlash('message', 'E-mail  de réinitialisation du mp envoyé :');
            //    return $this->redirectToRoute("app_login");


        }

        return $this->render('security/forgotPassword.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/resetpassword/{token}', name: 'app_reset_password')]

    public function resetpassword(Request $request, string $token,UserPasswordEncoderInterface $passwordEncoder,ManagerRegistry $doctrine)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if ($user == null) {
            $this->addFlash('danger', 'TOKEN INCONNU');
            return $this->redirectToRoute('app_login');

        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Mot de passe mis à jour :');
            return $this->redirectToRoute('app_login');

        } else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);

        }
    }

}

    

