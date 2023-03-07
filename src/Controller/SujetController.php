<?php

namespace App\Controller;
use App\Entity\Images;
use App\Entity\ReplaySujet;
use App\Entity\Specialites;
use App\Entity\Sujet;
use App\Entity\SujetLike;
use App\Form\ReplaySujetType;
use App\Form\ResoluSujetType;
use App\Form\SujetType;
use App\Repository\PatientRepository;
use App\Repository\ReplaySujetRepository;
use App\Repository\SpecialitesRepository;
use App\Repository\SujetRepository;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[Route('/sujet')]
class SujetController extends AbstractController
{

    #[Route('/listetest', name: 'app_sujet_liste')]
    public function ListeSpecialite(SpecialitesRepository $specialite,NormalizerInterface $serializerInterface): Response
    {    $specialite=$specialite->findAll();
        $specialiteNormailize=$serializerInterface->normalize($specialite,'json',['groups'=>"specialites"]);
        $json=json_encode($specialiteNormailize);
    return  new response($json);
        
    }
    #[Route('/sujet', name: 'app_sujet_new_json')]
    public function addSujetJson(Request $request, SujetRepository $sujetRepository,PatientRepository $patientRepository,NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $sujet = new Sujet();
        $idUser=$this->getUser(); 
        $UserConnecter=$patientRepository->findOneBy(['id'=> $idUser]);
        $userNormailize=$normalizerInterface->normalize($UserConnecter,'json',['groups'=>"user"]);

        $sujet->setTitle($request->get('title'));
        $sujet->setMessage($request->get('message'));
        $sujet->setDescription($request->get('description'));
        $em->persist($sujet);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($sujet,'json',['groups'=>'sujets']);
        return new Response(json_encode($jsonContent));

      
       

    }


    #[Route('/sujet/{id}/modifier', name: 'app_sujet_modifier_json')]
    public function ModifierSujetJson(Request $request, $id,SujetRepository $sujetRepository,PatientRepository $patientRepository,NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $sujet=$em->getRepository(Sujet::class)->find($id);

        $sujet->setTitle($request->get('title'));
        $sujet->setMessage($request->get('message'));
        $sujet->setDescription($request->get('description'));
        
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($sujet,'json',['groups'=>'sujets']);
        return new Response(json_encode($jsonContent));

      
       

    }
    #[Route('/', name: 'app_sujet_index', methods: ['GET'])]
    public function index(SpecialitesRepository $specialite): Response
    {   
        return $this->render('sujet/specialite.html.twig', [
            'specialites' => $specialite->findAll(),
        ]);
    }

    #[Route('/specialite/{id}', name: 'app_sujet_list',  methods: ['GET', 'POST'])]

    public function liste($id,Specialites $categorie,Request $request,SujetRepository $sujetRepository,PatientRepository $patientRepository,PaginatorInterface $paginator )
    {  
         $idUser=$this->getUser(); 
          
        $UserConnecter=$patientRepository->findOneBy(['id'=> $idUser]);
        $pay = $sujetRepository->findBySpecialites($categorie,['title' => 'desc']) ;

        
        $pay = $paginator->paginate(
            $pay, /* query NOT result */
            $request->query->getInt('page', 1),
            3
        );
        

        return $this->render('sujet/liste_question.html.twig', [
            'sujets' =>$sujetRepository->findBySpecialites($categorie),
            'specialites' => $categorie,
            'users'=>$UserConnecter,
            'id'=>$id,
            'sujets'=>$pay,

            
            
        ]);
    }
    #[Route('/new/{id}', name: 'app_sujet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SujetRepository $sujetRepository,$id,PatientRepository $patientRepository): Response
    {   
        
        $idUser=$this->getUser(); 
        $sujet = new Sujet();
        $UserConnecter=$patientRepository->findOneBy(['id'=> $idUser]);
      
       
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);
       

        if ($form->isSubmitted() && $form->isValid()) {
           
            $sujet->setUtilisateur($UserConnecter);
            $sujetRepository->save($sujet, true);
           
            return $this->redirectToRoute('app_sujet_index')
        ;
   }

        return $this->renderForm('sujet/new.html.twig', [
            'sujet' => $sujet,
            'form' => $form,
         
           
        
        ]);
    }
    #[Route('/{id}/test', name: 'app_sujet_avoir', methods: ['GET', 'POST'])]

    public function voir(Request $request,$id, Sujet $sujet,ReplaySujetRepository $replay ,PatientRepository $patientRepository,PaginatorInterface $paginator)
    {
        $idUser=$this->getUser();
        $reponse = new ReplaySujet();
        $form = $this->createForm(ReplaySujetType::class, $reponse);

        $PatientSelectioner = $patientRepository->findOneBy(['id' => $id]); 
        $UserConnecter=$patientRepository->findOneBy(['id'=> $idUser]);
        $pay = $replay->findBySujet($sujet);

        $pay = $paginator->paginate(
            $pay, /* query NOT result */
            $request->query->getInt('page', 1),
            3
        );
        
        $form->handleRequest($request);
        $countreponse = count($replay->findBySujet($id));
        if ($form->isSubmitted() && $form->isValid()) {
             //on recupere les images transmises
             $images = $form->get('images')->getData();
             // On boucle sur les images
             foreach($images as $image){
                 // On génère un nouveau nom de fichier
                 $fichier = md5(uniqid()).'.'.$image->guessExtension();
                 
                 // On copie le fichier dans le dossier uploads
                 $image->move(
                     $this->getParameter('images_directory'),
                     $fichier
                 );
                 
                 // On crée l'image dans la base de données
                 $img = new Images();
                 $img->setName($fichier);
                 $img->setUrl($fichier);
                 $reponse->addImage($img);
                }
                $UserConnecter=$patientRepository->findOneBy(['id'=> $idUser]);
            $em = $this->getDoctrine()->getManager();
            
            $reponse->setSujet($sujet);
            $reponse->setDate(new \DateTime());
            $reponse->setUtilisateur($UserConnecter);

            
            $em->persist($reponse);
            $em->flush();

            $this->addFlash('success', 'Ajout avec succès');
            $replay->findBySujet($sujet);
            return $this->redirectToRoute('app_sujet_avoir',['id' => $id
             ]);
        }

        return $this->renderForm('sujet/reponse.html.twig', [
            'sujets' => $sujet,
            'replays' => $replay->findBySujet($sujet), 
            'form' => $form,
            'countreponse'=>$countreponse,
            'users'=>$UserConnecter,
            'id'=>$id,
            'patients'=>$PatientSelectioner,
            'replays'=>$pay
            


        ]);

    }

    #[Route('/{id}', name: 'app_sujet_show', methods: ['GET'])]
    public function show(Sujet $sujet): Response
    {
        return $this->render('sujet/show.html.twig', [
            'sujet' => $sujet,
        ]);
    }

#[Route('/{id}/edit', name: 'app_sujet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sujet $sujet, SujetRepository $sujetRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($sujet->getUtilisateur() != $this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujetRepository->save($sujet, true);

            return $this->redirectToRoute('app_sujet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sujet/edit.html.twig', [
            'sujet' => $sujet,
            'form' => $form,
        ]);
    }    

    #[Route('/{id}/delete', name: 'app_sujet_delete', methods: ['POST'])]
    public function delete(Request $request, Sujet $sujet, SujetRepository $sujetRepository): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($sujet->getUtilisateur() != $this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        if ($this->isCsrfTokenValid('delete'.$sujet->getId(), $request->request->get('_token'))) {
            $sujetRepository->remove($sujet, true);
        }

        return $this->redirectToRoute('app_sujet_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/editstatutsujet', name: 'app_statut_edit', methods: ['GET', 'POST'])]
    public function editStatutSujet(Request $request, Sujet $sujet, SujetRepository $sujetRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($sujet->getUtilisateur() != $this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        $form = $this->createForm(ResoluSujetType::class, $sujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujetRepository->save($sujet, true);

            return $this->redirectToRoute('app_sujet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('forum/sujetResolu.html.twig', [
            'sujet' => $sujet,
            'form' => $form,
        ]);
    } 
    
    #[Route('/sujet/{id}/like', name: 'sujet_like', methods: ['GET', 'POST'])]
    public function like(Sujet $sujet): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Vous devez être connecté pour liker un sujet.'], 403);
        }

        $sujetLike = $entityManager->getRepository(SujetLike::class)->findOneBy([
            'sujet' => $sujet,
            'user' => $user,
        ]);

        if (!$sujetLike) {
            $sujetLike = new SujetLike();
            $sujetLike->setSujet($sujet)
                ->setUser($user);
        }

        $sujetLike->setValue(SujetLike::LIKE);
        $entityManager->persist($sujetLike);
        $entityManager->flush();

        return $this->json(['count' => $sujet->getLikesCount()]);
    }
 
    #[Route('/article/{id}/undislike', name: 'sujet_undislike', methods: ['GET', 'POST'])]
    public function undislike(Sujet $sujet): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
    
        if (!$user) {
            return $this->json(['error' => 'Vous devez être connecté pour undisliker un sujet.'], 403);
        }
    
        $sujetLike = $entityManager->getRepository(SujetLike::class)->findOneBy([
            'sujet' => $sujet,
            'user' => $user,
        ]);
    
        if ($sujetLike) {
            $entityManager->remove($sujetLike);
            $entityManager->flush();
        }
    
        return $this->json(['count' => $sujet->getLikesCount()]);
    }
}