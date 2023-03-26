<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Form\DossierType;
use App\Repository\DossierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Images;
use App\Entity\Medecin;
use App\Entity\User;
use App\Entity\Patient;
use App\Repository\PatientRepository;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\DiagnostiqueRepository;
use Dompdf\Dompdf;
use Dompdf\Options;


#[Route('/dossier')]
class DossierController extends AbstractController
{
    #[Route('/', name: 'app_dossier_index', methods: ['GET'])]
    public function index(User $user,
    Request $request,
    PatientRepository $patientRepository,
    DiagnostiqueRepository $diagnostiqueRepository,
    DossierRepository $dossierRepository,
    PaginatorInterface $paginator
): Response {
    $medecin = $this->getUser();
    /*$queryBuilder = $consulationRepository->findByPatients($user);
    $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
    //$pagerfanta->setMaxPerPage(2);*/
   
    return $this->renderForm('consultation/index.html.twig', [
        //'consulationsMedecin' => $consulationRepository->listByMedecinEtPatient($medecin->getId(), $user->getId()),
        'diagnostique' => $diagnostiqueRepository->listByMedecinEtPatient($medecin->getId(), $user->getId()),
        'user' => $user,
        'patient' => $user,
        'dossiers' => $dossiers,
        'dossiers' => $dossierRepository->findAll()
    ]);
}

    #[Route('/new/{id_patient}', name: 'app_dossier_new', methods: ['GET', 'POST'])]
    public function new($id_patient,Request $request, PatientRepository $patientRepository,DossierRepository $dossierRepository, EntityManagerInterface $entityManager): Response
    {
        $medecin = $this->getUser();
        $dossier = new Dossier();
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $patient = $patientRepository->findOneBy(['id' => $id_patient]);
        $dossierRepository->save($dossier, true);           
        $dossier->setMedecin($medecin);
        $dossier->setPatient($patient);
        $entityManager->persist($dossier);
        $entityManager->flush();

            return $this->redirectToRoute('app_patient_show_consu', ['id' => $id_patient], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dossier/new.html.twig', [
            'dossier' => $dossier,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_dossier_show', methods: ['GET'])]
    public function show(Dossier $dossier): Response
    {
        return $this->render('dossier/show.html.twig', [
            'dossier' => $dossier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dossier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dossier $dossier, $patient,DossierRepository $dossierRepository): Response
    {
        $medecin = $this->getUser();
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dossierRepository->save($dossier, true);
            $dossier->setMedecin($medecin);
            $dossier->setPatient($patient);


            return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('consultation/index.html.twig', [
            'dossier' => $dossier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dossier_delete', methods: ['POST'])]
    public function delete(Request $request, Dossier $dossier, DossierRepository $dossierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dossier->getId(), $request->request->get('_token'))) {
            $dossierRepository->remove($dossier, true);
        }

        return $this->redirectToRoute('app_dossier_index', [], Response::HTTP_SEE_OTHER);
    }
}