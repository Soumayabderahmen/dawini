<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisType;
use App\Entity\Medecin;
use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use App\Repository\MedecinRepository;
use App\Repository\PatientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
   
    #[Route('/', name: 'app_front')]
    public function index(MedecinRepository  $userRepository): Response
    {
        
        return $this->render('home.html.twig', [
            'medecins' => $userRepository->findAll(),
            'users' => $userRepository->findAll(),
            
        
        ]);
    }

    #[Route('/profil/{id}', name: 'app_profil')]
    public function avis(MedecinRepository $medecinRepository, User $user,PatientRepository $patientRepository,Request $request,$id,AvisRepository $avisRepository): Response
    {  
        $medecin = $medecinRepository->findById($id);
        $medecinSelectioner = $medecinRepository->findOneBy(['id' => $id]);

       $iduser=$this->getUser();
      
        $avi = new Avis();
        $avis=$avisRepository->findByMedecin($id);
        $countavis = count($avisRepository->findByMedecin($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $medecinSelectioner = $medecinRepository->findOneBy(['id' => $id]);
            $patientConnecter = $patientRepository->findOneBy(['id' => $iduser]);

            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("Activer");
            $avi->setMedecin($medecinSelectioner);
            $avi->setPatient($patientConnecter);
            $avisRepository->save($avi, true);
            
            return $this->redirectToRoute('app_profil' ,['id'=>$id]);
        }
        return $this->renderForm('front/avis.html.twig', [
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
            'medecin'=>$medecin,
        ]);
    }


}
