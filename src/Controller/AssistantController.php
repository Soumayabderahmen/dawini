<?php

namespace App\Controller;

use App\Entity\Assistant;
use App\Entity\Medecin;
use App\Entity\User;
use App\Form\AssistantType;
use App\Form\ChangePasswordType;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/assistant')]
class AssistantController extends AbstractController
{
    private $userPasswordEncoder;
    public function __construct( UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    #[Route('/', name: 'app_assistant_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('assistant/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_assistant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
        $user = new Assistant();
        $form = $this->createForm(AssistantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            $this->addFlash('success','Ajout avec Success');
            if ($user->getPassword()&&$user->getConfirmPassword()) {
                $user->setPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
                );
                $user->setConfirmPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getConfirmPassword())
                );
                $user->eraseCredentials();
            }
         
            $roles[]='ROLE_ASSISTANT';
            $user->setRoles($roles);
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_assistant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('assistant/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
//Mobile
#[Route('/All', name: 'app_assistants_liste')]
public function Listeassistant(UserRepository $assistant, SerializerInterface $serializer)
{
    $assistant = $assistant->findAll();
    $assistantNormailize = $serializer->serialize($assistant, 'json', ['groups' => "medecin"]);

    $json = json_encode($assistantNormailize);
    return  new response($json);
}
#[Route('/assistantJson/{id}', name: 'app_assistant_seule')]
public function assistantId($id,UserRepository $assistant, SerializerInterface $serializer)
{
    $assistant = $assistant->find($id);
    $assistantNormailize = $serializer->serialize($assistant, 'json', ['groups' => "medecin"]);

    $json = json_encode($assistantNormailize);
    return  new response($json);
}

#[Route('/delete/AssistantJson/{id}', name: 'app_assistant_delete_seule')]
public function deleteassistantJson($id,UserRepository $assistant, NormalizerInterface $normalizerInterface,Request $request)
{
    $em=$this->getDoctrine()->getManager();
    $assistant=$em->getRepository(assistant::class)->find($id);
    $em->remove($assistant);
    $em->flush();
    $jsonContent=$normalizerInterface->normalize($assistant,'json',['groups'=>'medecin']);
    return  new Response("assistant deleted successfully". json_encode($jsonContent));
}
#[Route('/add/AssistantJson', name: 'app_assistant_new_json')]
public function addassistantJson(Request $request, NormalizerInterface $normalizerInterface): Response
{   
    $em=$this->getDoctrine()->getManager();
    $assistant = new assistant();
    $assistant->setEmail($request->get('email'));
    $assistant->setPassword($request->get('password'));
    $assistant->setConfirmPassword($request->get('confirm_password'));
    $assistant->setNom($request->get('nom'));
    $assistant->setPrenom($request->get('prenom'));
    $assistant->setCin($request->get('cin'));
    $assistant->setSexe($request->get('sexe'));
    $assistant->setTelephone($request->get('telephone'));
    $assistant->setGouvernorat($request->get('gouvernorat'));
    $assistant->setAdresse($request->get('adresse'));
    $assistant->setImage($request->get('photo'));
    $em->persist($assistant);
    $em->flush();
    $jsonContent=$normalizerInterface->normalize($assistant,'json',['groups'=>'medecin']);
    return new Response(json_encode($jsonContent));

  
   

}
#[Route('/edit/{id}/assistantJson', name: 'app_assistant_edit_json')]
public function editAssistantJson(Request $request, $id,NormalizerInterface $normalizerInterface): Response
{   
    $em=$this->getDoctrine()->getManager();
    $assistant=$em->getRepository(Assistant::class)->find($id);
   
    $assistant->setEmail($request->get('email'));
    $assistant->setPassword($request->get('password'));
    $assistant->setConfirmPassword($request->get('confirm_password'));
    $assistant->setNom($request->get('nom'));
    $assistant->setPrenom($request->get('prenom'));
    $assistant->setCin($request->get('cin'));
    $assistant->setSexe($request->get('sexe'));
    $assistant->setTelephone($request->get('telephone'));
    $assistant->setGouvernorat($request->get('gouvernorat'));
    $assistant->setAdresse($request->get('adresse'));
    $assistant->setImage($request->get('photo'));
   
    

   
    $em->flush();
    $jsonContent=$normalizerInterface->normalize($assistant,'json',['groups'=>'medecin']);
    return new Response(json_encode($jsonContent));

  
   

}


  




    #[Route('/{id}', name: 'app_assistant_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('assistant/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_assistant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AssistantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_assistant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('assistant/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_assistant_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_assistant_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/profile/assistant', name: 'app_assistant_profile')]
    public function profile(Request $request,SluggerInterface $slugger): Response
    {

        $assistant = $this->getUser();

        if ($assistant instanceof Assistant) {
            $form = $this->createForm(UserProfileType::class, $assistant);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
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
                    $assistant->setImage($newFilename);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash('success', 'Profil mis à jour avec succès.');

                return $this->redirectToRoute('app_assistant_profile');
            }

            return $this->render('profile/assistant_profile.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        throw new \LogicException('Erreur : l\'utilisateur courant n\'est pas un assistant.');
    
        // return $this->render('profile/med_profile.html.twig');
    }




#[Route('/assistant/change-password', name: 'app_assistant_change-password')]
    public function changePassword(Request $request)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $password = $form->get('password')->getData();
            $ConfirmPassword = $form->get('confirm_password')->getData();
            $encoder = $this->userPasswordEncoder->encodePassword($user, $password, $ConfirmPassword);
            $user->setPassword($encoder);
            $user->setConfirmPassword($encoder);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');

            return $this->redirectToRoute('app_assistant_profile');
        }

        return $this->render('assistant/change_password_assistant.html.twig', [
            'form' => $form->createView(),
        ]);
    }


 












    // #[Route('/newAss', name: 'medecin_ajouter_assistant', methods: ['GET', 'POST'])]
    // public function ajouterAssistant(Request $request, Medecin $medecin,UserRepository $userRepository,SluggerInterface $slugger): Response
    // {
    //     $assistant = new Assistant();
    //     $form = $this->createForm(AssistantType::class, $assistant);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $photo = $form->get('photo')->getData();

    //         // this condition is needed because the 'brochure' field is not required
    //         // so the PDF file must be processed only when a file is uploaded
    //         if ($photo) {
    //             $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
    //             // this is needed to safely include the file name as part of the URL
    //             $safeFilename = $slugger->slug($originalFilename);
    //             $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

    //             // Move the file to the directory where brochures are stored
    //             try {
    //                 $photo->move(
    //                     $this->getParameter('images_directory'),
    //                     $newFilename
    //                 );
    //             } catch (FileException $e) {
                 
    //             }

    //             // updates the 'brochureFilename' property to store the PDF file name
    //             // instead of its contents
    //             $assistant->setImage($newFilename);
    //         }
    //         $this->addFlash('success','Ajout avec Success');
    //         if ($assistant->getPassword()&&$assistant->getConfirmPassword()) {
    //             $assistant->setPassword(
    //                 $this->userPasswordEncoder->encodePassword($assistant, $assistant->getPassword())
    //             );
    //             $assistant->setConfirmPassword(
    //                 $this->userPasswordEncoder->encodePassword($assistant, $assistant->getConfirmPassword())
    //             );
    //             $assistant->eraseCredentials();
    //         }
    //         $roles[]='ROLE_ASSISTANT';
    //         $assistant->setRoles($roles);
    //         $userRepository->save($assistant, true);

    //         return $this->redirectToRoute('app_assistant_index', ['id' => $medecin->getId()], Response::HTTP_SEE_OTHER);
           
    //        // return $this->redirectToRoute('medecin_afficher', ['id' => $medecin->getId()]);
    //     }

    //     return $this->renderForm('assistant/new.html.twig', [
    //         'user' => $assistant,
    //         'form' => $form,
    //     ]);

    //     }





}