<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\MedecinRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function index(MedecinRepository $userRepository): Response
    {
        return $this->render('home.html.twig', [
            'medecins' => $userRepository->findAll(),
        ]);
    }

    #[Route('/profil/{id}', name: 'app_profil')]
    public function avis(MedecinRepository $medecinRepository, Request $request,$id,AvisRepository $avisRepository): Response
    {    
        $medecin = $medecinRepository->findById($id);
       
       
        $avi = new Avis();
        $avis=$avisRepository->findByMedecin($id);
        $countavis = count($avisRepository->findByMedecin($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $medecinSelectioner = $medecinRepository->findOneBy(['id' => $id]);
            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("Activer");
            $avi->setMedecin($medecinSelectioner);

            $avisRepository->save($avi, true);
            
            return $this->redirectToRoute('app_profil' ,['id'=>$id]);
        }
        return $this->renderForm('front/avis.html.twig', [
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
        ]);
    }
}
