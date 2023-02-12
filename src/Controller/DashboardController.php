<?php

namespace App\Controller;

use App\Repository\MedecinRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/test2', name: 'Dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Dashboard/dashboardAdmin.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/list', name: 'app_list')]
    public function list(MedecinRepository $userRepository): Response
    {
        return $this->render('Medecin/index.html.twig', [
            'medecins' => $userRepository->findAll(),
        ]);
    }
    #[Route('/dash', name: 'app_dash')]
    public function dash(MedecinRepository $userRepository): Response
    {
        return $this->render('Dashboard/dashboardMedecin.html.twig', [
            'medecins' => $userRepository->findAll(),
        ]);
    }

    #[Route('/assis', name: 'app_dashAss')]
    public function dashAss(MedecinRepository $userRepository): Response
    {
        return $this->render('Dashboard/dashboardAssistant.html.twig', [
            'medecins' => $userRepository->findAll(),
        ]);
    }
    #[Route('/pat', name: 'app_dashpat')]
    public function dashpat(MedecinRepository $userRepository): Response
    {
        return $this->render('Dashboard/dashboardPatient.html.twig', [
            'medecins' => $userRepository->findAll(),
        ]);
    }
}
