<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Medecin;
use App\Entity\User;
use App\Form\UserProfileType;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/admin8')]
class AdminController extends AbstractController
{
    private $userPasswordEncoder;
    public function __construct( UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    
    {
        $user=$this->getUser();
        $role=$user->getRoles();
        if (in_array("ROLE_ASSISTANT", $role)) 
            return $this->redirectToRoute('app_front');
            if (in_array("ROLE_PATIENT", $role)) 
              return $this->redirectToRoute('app_front');
              if (in_array("ROLE_MEDECIN", $role)) 
              return $this->redirectToRoute('app_front');
              if (in_array("ROLE_ADMIN", $role)) 
              return $this->redirectToRoute('Dashboard');
        return $this->render('Dashboard/dashboardAdmin.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
        $user = new Admin();
         $roles = $user->getRoles();
        $form = $this->createForm(UserType::class, $user);
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
            $roles[]='ROLE_ADMIN';
           
            $user->setRoles($roles);
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    //Mobile
#[Route('/All', name: 'app_admins_liste')]
public function ListeAdmin(UserRepository $admin, SerializerInterface $serializer)
{
    $admin = $admin->findAll();
    $adminNormailize = $serializer->serialize($admin, 'json', ['groups' => "medecin"]);

    $json = json_encode($adminNormailize);
    return  new response($json);
}
#[Route('/AdminJson/{id}', name: 'app_admin_seule')]
public function adminId($id,UserRepository $admin, SerializerInterface $serializer)
{
    $admin = $admin->find($id);
    $adminNormailize = $serializer->serialize($admin, 'json', ['groups' => "medecin"]);

    $json = json_encode($adminNormailize);
    return  new response($json);
}

#[Route('/delete/AdminJson/{id}', name: 'app_admin_delete_seule')]
public function deleteAdminJson($id,UserRepository $admin, NormalizerInterface $normalizerInterface,Request $request)
{
    $em=$this->getDoctrine()->getManager();
    $admin=$em->getRepository(Admin::class)->find($id);
    $em->remove($admin);
    $em->flush();
    $jsonContent=$normalizerInterface->normalize($admin,'json',['groups'=>'medecin']);
    return  new Response("admin deleted successfully". json_encode($jsonContent));
}
#[Route('/add/AdminJson', name: 'app_admin_new_json')]
public function addAdminJson(Request $request, NormalizerInterface $normalizerInterface): Response
{   
    $em=$this->getDoctrine()->getManager();
    $admin = new Admin();
    $admin->setEmail($request->get('email'));
    $admin->setPassword($request->get('password'));
    $admin->setConfirmPassword($request->get('confirm_password'));
    $admin->setNom($request->get('nom'));
    $admin->setPrenom($request->get('prenom'));
    $admin->setCin($request->get('cin'));
    $admin->setSexe($request->get('sexe'));
    $admin->setTelephone($request->get('telephone'));
    $admin->setGouvernorat($request->get('gouvernorat'));
    $admin->setAdresse($request->get('adresse'));
    $admin->setImage($request->get('photo'));
    $em->persist($admin);
    $em->flush();
    $jsonContent=$normalizerInterface->normalize($admin,'json',['groups'=>'medecin']);
    return new Response(json_encode($jsonContent));

  
   

}
#[Route('/edit/{id}/AdminJson', name: 'app_admin_edit_json')]
public function editAdminJson(Request $request, $id,NormalizerInterface $normalizerInterface): Response
{   
    $em=$this->getDoctrine()->getManager();
    $admin=$em->getRepository(Admin::class)->find($id);
   
    $admin->setEmail($request->get('email'));
    $admin->setPassword($request->get('password'));
    $admin->setConfirmPassword($request->get('confirm_password'));
    $admin->setNom($request->get('nom'));
    $admin->setPrenom($request->get('prenom'));
    $admin->setCin($request->get('cin'));
    $admin->setSexe($request->get('sexe'));
    $admin->setTelephone($request->get('telephone'));
    $admin->setGouvernorat($request->get('gouvernorat'));
    $admin->setAdresse($request->get('adresse'));
    $admin->setImage($request->get('photo'));
   
    

   
    $em->flush();
    $jsonContent=$normalizerInterface->normalize($admin,'json',['groups'=>'medecin']);
    return new Response(json_encode($jsonContent));

  
   

}


  




    #[Route('/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UserType::class, $user);
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

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/profile/admin', name: 'app_admin_profile')]
    public function profile(Request $request,SluggerInterface $slugger): Response
    {

        $admin = $this->getUser();

        if ($admin instanceof Admin) {
            $form = $this->createForm(UserProfileType::class, $admin);
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
                    $admin->setImage($newFilename);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash('success', 'Profil mis à jour avec succès.');

                return $this->redirectToRoute('app_admin_profile');
            }

            return $this->render('profile/admin_profile.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        throw new \LogicException('Erreur : l\'utilisateur courant n\'est pas un admin.');
    
        // return $this->render('profile/med_profile.html.twig');
    }

    #[Route('/admin/change-password', name: 'app_admin_change-password')]
    public function changePassword(Request $request )
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

            return $this->redirectToRoute('app_admin_profile');
        }

        return $this->render('admin/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

 
   


    //partie Mobile

    
}