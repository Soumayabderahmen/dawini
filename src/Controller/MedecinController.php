<?php

namespace App\Controller;


use App\Entity\Medecin;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\MedecinType;
use App\Form\Medecin1Type;
use App\Form\MedProfileType;
use App\Repository\AssistantRepository;
use App\Repository\MedecinRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/medecin')]
class MedecinController extends AbstractController
{
    private $userPasswordEncoder;
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    #[Route('/', name: 'app_medecin_index', methods: ['GET'])]
    public function index(MedecinRepository $userRepository): Response
    {
        return $this->render('medecin/medecin.html.twig', [
            'medecins' => $userRepository->findAll(),
        ]);
    }

    #[Route('/assistante', name: 'medecin_assistant_index', methods: ['GET'])]
    public function indexAssistant(AssistantRepository $userRepository): Response
    {
        $medecin = $this->getUser();
        return $this->render('medecin/assistant.html.twig', [
            'assistant' => $userRepository->findByMedecin($medecin),
        ]);
    }




    #[Route('/new', name: 'app_medecin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MedecinRepository $userRepository, SluggerInterface $slugger): Response
    {
        $medecin = new Medecin();
        $form = $this->createForm(MedecinType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

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
                $medecin->setImage($newFilename);
            }
            $this->addFlash('success', 'Ajout avec Success');
            if ($medecin->getPassword() && $medecin->getConfirmPassword()) {
                $medecin->setPassword(
                    $this->userPasswordEncoder->encodePassword($medecin, $medecin->getPassword())
                );
                $medecin->setConfirmPassword(
                    $this->userPasswordEncoder->encodePassword($medecin, $medecin->getConfirmPassword())
                );
                $medecin->eraseCredentials();
            }

            $roles[] = 'ROLE_MEDECIN';
            $medecin->setRoles($roles);
            $userRepository->save($medecin, true);

            return $this->redirectToRoute('app_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('medecin/new.html.twig', [
            'user' => $medecin,
            'form' => $form,
        ]);
    }
    // partie mobile
    #[Route('/All', name: 'app_medecins_liste')]
    public function ListeMedecin(MedecinRepository $medecin, SerializerInterface $serializer)
    {
        $medecin = $medecin->findAll();
        $medecinNormailize = $serializer->serialize($medecin, 'json', ['groups' => "medecin"]);

        $json = json_encode($medecinNormailize);
        return  new response($json);
    }
    #[Route('/medecinJson/{id}', name: 'app_medecin_seule')]
    public function MedecinId($id,MedecinRepository $medecin, SerializerInterface $serializer)
    {
        $medecin = $medecin->find($id);
        $medecinNormailize = $serializer->serialize($medecin, 'json', ['groups' => "medecin"]);

        $json = json_encode($medecinNormailize);
        return  new response($json);
    }

    #[Route('/delete/medecinJson/{id}', name: 'app_medecin_delete_seule')]
    public function deleteMedecinJson($id,MedecinRepository $medecin, NormalizerInterface $normalizerInterface,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $medecin=$em->getRepository(Medecin::class)->find($id);
        $em->remove($medecin);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($medecin,'json',['groups'=>'medecin']);
        return  new Response("Medecin deleted successfully". json_encode($jsonContent));
    }
    #[Route('/add/MedecinJson', name: 'app_medecin_new_json')]
    public function addMedecinJson(Request $request, NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $medecin = new Medecin();
       
        $medecin->setEmail($request->get('email'));
        $medecin->setPassword($request->get('password'));
        $medecin->setConfirmPassword($request->get('confirm_password'));
        $medecin->setNom($request->get('nom'));
        $medecin->setPrenom($request->get('prenom'));
        $medecin->setCin($request->get('cin'));
        $medecin->setSexe($request->get('sexe'));
        $medecin->setTelephone($request->get('telephone'));
        $medecin->setGouvernorat($request->get('gouvernorat'));
        $medecin->setAdresse($request->get('adresse'));
        $medecin->setImage($request->get('photo'));
        $medecin->setTitre($request->get('titre'));
        $medecin->setAdresseCabinet($request->get('adresse_cabinet'));
        $medecin->setFixe($request->get('fixe'));
        $medecin->setDiplomeFormation($request->get('diplome_formation'));
        $medecin->setTarif($request->get('tarif'));
        $medecin->setCnam($request->get('cnam'));
        $medecin->setSpecialites($request->get('specialites'));

        $em->persist($medecin);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($medecin,'json',['groups'=>'medecin']);
        return new Response(json_encode($jsonContent));

      
       

    }
    #[Route('/edit/{id}/MedecinJson', name: 'app_medecin_edit_json')]
    public function editMedecinJson(Request $request, $id,NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $medecin=$em->getRepository(Medecin::class)->find($id);
       
        $medecin->setEmail($request->get('email'));
        $medecin->setPassword($request->get('password'));
        $medecin->setConfirmPassword($request->get('confirm_password'));
        $medecin->setNom($request->get('nom'));
        $medecin->setPrenom($request->get('prenom'));
        $medecin->setCin($request->get('cin'));
        $medecin->setSexe($request->get('sexe'));
        $medecin->setTelephone($request->get('telephone'));
        $medecin->setGouvernorat($request->get('gouvernorat'));
        $medecin->setAdresse($request->get('adresse'));
        $medecin->setImage($request->get('photo'));
        $medecin->setTitre($request->get('titre'));
        $medecin->setAdresseCabinet($request->get('adresse_cabinet'));
        $medecin->setFixe($request->get('fixe'));
        $medecin->setDiplomeFormation($request->get('diplome_formation'));
        $medecin->setTarif($request->get('tarif'));
        $medecin->setCnam($request->get('cnam'));
        

       
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($medecin,'json',['groups'=>'medecin']);
        return new Response(json_encode($jsonContent));

      
       

    }

   


    #[Route('/{id}', name: 'app_medecin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('medecin/show.html.twig', [
            'medecin' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_medecin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, SluggerInterface $slugger, Medecin $medecin): Response
    {
        $form = $this->createForm(Medecin1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_medecin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('medecin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_medecin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_medecin_index', [], Response::HTTP_SEE_OTHER);
    }




    #[Route('/profile/medecin', name: 'app_medecin_profile')]
    public function profile(Request $request, SluggerInterface $slugger): Response
    {

        $medecin = $this->getUser();

        if ($medecin instanceof Medecin) {
            $form = $this->createForm(MedProfileType::class, $medecin);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $photo = $form->get('photo')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($photo) {
                    $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

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
                    $medecin->setImage($newFilename);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash('success', 'Profil mis à jour avec succès.');


                return $this->redirectToRoute('app_medecin_profile');
            }

            return $this->render('profile/med_profile.html.twig', [
                'form' => $form->createView(),
                // 'medecins' => $userRepository->findByImage($medecin),
            ]);
        }

        throw new \LogicException('Erreur : l\'utilisateur courant n\'est pas un médecin.');

        // return $this->render('profile/med_profile.html.twig');
    }
    // je suis une Alela tres grande et je mange le chokotome jour et nuis  
    #[Route('/medecin/change-password', name: 'app_medecin_change-password')]
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

            return $this->redirectToRoute('app_medecin_profile');
        }

        return $this->render('medecin/change_password_medecin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

 
#[Route('/medecins/search', name: 'medecins_search', methods: ['GET', 'POST'])]
public function search(Request $request, MedecinRepository $medecinRepository): JsonResponse
{
    // Récupérer le mot clé envoyé par la requête AJAX
    $searchTerm = $request->get('search');

    // Rechercher les médecins correspondants au mot clé
    $medecins = $medecinRepository->searchByTerm($searchTerm);

    // Construire un tableau de données pour la réponse JSON
    $data = [];
    foreach ($medecins as $medecin) {
        $data[] = [
            'nom' => $medecin->getNom(),
            'prenom' => $medecin->getPrenom(),
            'email' => $medecin->getEmail(),
            'telephone' => $medecin->getTelephone(),
            'tarif_fixe' => $medecin->getTarifFixe(),
        ];
    }

    // Retourner la réponse JSON
    return new JsonResponse(['data' => $data]);
}

}
