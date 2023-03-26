<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\User;
use App\Entity\Patient;
use App\Entity\Ordonnance;
use App\Entity\Consulation;
use App\Form\ConsulationType;
use App\Form\ConsultationChercherTypeType;
use App\Form\ConsultationPatientType;
use App\Form\OrdonnanceType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Repository\ConsulationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrdonnanceRepository;
use App\Repository\DossierRepository;

use App\Repository\PatientRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;

use Knp\Component\Pager\PaginatorInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

#[Route('/consultation')]
class ConsultationController extends AbstractController
{
    #[Route('/client', name: 'app_consultation_client', methods: ['GET'])]
    public function client(ConsulationRepository $consulationRepository, OrdonnanceRepository $ordonnanceRepository, EntityManagerInterface $entityManager): Response
    {

        return $this->render('consultation/client.html.twig', []);
    }



    #[Route('/{id}/patient', name: 'app_patient_show_consu', methods: ['GET'])]
    public function showPatient(
        User $user,
        Request $request,
        ConsulationRepository $consulationRepository,
        PatientRepository $patientRepository,
        OrdonnanceRepository $ordonnanceRepository,
        DossierRepository $dossierRepository,
        PaginatorInterface $paginator
    ): Response {
        $medecin = $this->getUser();
        /*$queryBuilder = $consulationRepository->findByPatients($user);
        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        //$pagerfanta->setMaxPerPage(5);*/
        $donnees = $consulationRepository->findBy(['patients' => $user, 'medecin' => $medecin]);
        $consultations = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            3 // Nombre de résultats par page
        );
        $dossiers = $dossierRepository->findAll();
        $dossiers = $paginator->paginate(
            $dossiers, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            1 // Nombre de résultats par page
        );
        return $this->renderForm('consultation/index.html.twig', [
            //'consulationsMedecin' => $consulationRepository->listByMedecinEtPatient($medecin->getId(), $user->getId()),
            'ordonnances' => $ordonnanceRepository->listByMedecinEtPatient($medecin->getId(), $user->getId()),
            'user' => $user,
            'patient' => $user,
            'consultations' => $consultations,
            'dossiers' => $dossiers
        ]);
    }





    #[Route('/', name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsulationRepository $consulationRepository, OrdonnanceRepository $ordonnanceRepository, Request $request): Response
    {
        /** @var Medecin $medecin */
        $medecin = $this->getUser();

        //recherche 
        $formChercher = $this->createForm(ConsultationChercherTypeType::class, null);
        $formChercher->handleRequest($request);
        // récupérer l'ID du patient depuis la requête
        return $this->render('consultation/index.html.twig', [
            'consulationsMedecin' => $consulationRepository->listByMedecinEtPatient($medecin->getId()),
            'ordonnances' => $ordonnanceRepository->findAll(),
            'formChercher' => $formChercher->createView(),

        ]);
    }

    #[Route('/listpatient', name: 'app_medecin_listpatient', methods: ['GET'])]
    public function listpatient(PatientRepository $patientRepository): Response
    {
        return $this->render('medecin/listpatient.html.twig', [
            'patients' => $patientRepository->findAll(),
        ]);
    }

    #[Route('/dashbordconsultation', name: 'app_consultation_dashboard', methods: ['GET'])]
    public function dashboard(ConsulationRepository $consulationRepository, OrdonnanceRepository $ordonnanceRepository): Response
    {
        return $this->render('consultation/dashboard.html.twig', [
            'consulations' => $consulationRepository->findAll(),


        ]);
    }

    #[Route('/new/patient', name: 'app_consultation_newPatient', methods: ['GET', 'POST'])]
    public function newPatient(Request $request, ConsulationRepository $consulationRepository, OrdonnanceRepository $ordonnanceRepository, EntityManagerInterface $entityManager): Response
    {
        $patients = $this->getUser();
        $consulation = new Consulation();
        $consulation->setDate(new \DateTime());
        $consulation->setPatients($patients);
        $form = $this->createForm(ConsulationType::class, $consulation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $consulationRepository->save($consulation, true);
            return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('consultation/new.html.twig', [
            'consulation' => $consulation,
            'form' => $form,
        ]);
    }

    #[Route('/new/{id_patient}', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ConsulationRepository $consulationRepository, $id_patient, PatientRepository $patientRepository, EntityManagerInterface $entityManager): Response
    {
        $medecin = $this->getUser();
        $patient = $patientRepository->findOneBy(['id' => $id_patient]);

        $consulation = new Consulation();
        $consulation->setMedecin($medecin);
        $consulation->setPatients($patient);
        $consulation->setDate(new \DateTime());
        $heureDebut = new \DateTime();
        $form = $this->createForm(ConsultationPatientType::class, null);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['ordonnance']->get('imageFile')->getData();

            $newFilename      = uniqid() . '-' . $imageFile->guessExtension();
            try {
                $imageFile->move(
                    //l'enplacement de l'image
                    $this->getParameter('od_directory'),
                    //le  nouveau nom d'image 
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // instead of its contents
            $heureFin = new \DateTime();
            $consulation->setHeuredebut($heureDebut);
            $consulation->setHeurefin($heureFin);
            $entityManager->persist($consulation);
            $orandance = $form['ordonnance']->getData();
            $orandance->setDate($heureFin);
            $orandance->setConsulation($consulation);
            $orandance->setImage($newFilename);
            $entityManager->persist($orandance);
            $entityManager->flush();
            //$consulationRepository->save($consulation, true);
            return $this->redirectToRoute('app_patient_show_consu', ['id' => $id_patient], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('consultation/new.html.twig', [
            'consulation' => $consulation,
            'form' => $form,
        ]);
    }

    #[Route('/suivant/{id}', name: 'continue_consultation', methods: ['GET', 'POST'])]
    public function suivant(Consulation $consulation, Request $request, ConsulationRepository $consulationRepository, PatientRepository $patientRepository, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(ConsultationPatientType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['ordonnance']->get('imageFile')->getData();
            $newFilename      = uniqid() . '-' . $imageFile->guessExtension();
            try {
                $imageFile->move(
                    //l'emplacement de l'image
                    $this->getParameter('od_directory'),
                    //le nouveau nom d'image 
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            // instead of its contents
            $heureFin = new \DateTime();
            $consulation->setHeurefin($heureFin);
            $consulation->setEstTermine(true);
            //$entityManager->persist($consulation);
            $orandance = $form['ordonnance']->getData();
            $orandance->setDate($heureFin);
            $orandance->setConsulation($consulation);
            $orandance->setImage($newFilename);
            $entityManager->persist($orandance);
            $entityManager->flush();
            //$consulationRepository->save($consulation, true);
            return $this->redirectToRoute('app_patient_show_consu', ['id' => $consulation->getPatients()->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('consultation/new.html.twig', [
            'consulation' => $consulation,
            'form' => $form,
        ]);
    }
    // commancer consultation
    #[Route("/new/{id}/commencer", name: 'commencer_consultation', methods: ['GET', 'POST'])]
    public function commencerCOnsultation(User $patient, Request $request, EntityManagerInterface $entityManager)
    {
        $medecin = $this->getUser();
        $form = $this->createFormBuilder(null)->add('urlConsultation', UrlType::class, [
            'attr' => [
                'class' => 'form-control'
            ]
        ])->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $dataReceived = $form->getData();
            $urlConsul = $dataReceived['urlConsultation'];
            $consultation = new Consulation();
            $consultation->setUrlConsultation($urlConsul);
            $heureFin = new \DateTime();
            $consultation->setMedecin($medecin);
            $consultation->setPatients($patient);
            $consultation->setDate(new \DateTime());
            $consultation->setHeuredebut(new DateTime());
            $consultation->setHeurefin($heureFin);

            $entityManager->persist($consultation);

            $entityManager->flush();
            return $this->redirectToRoute('continue_consultation', ['id' => $consultation->getId()]);
            /* dd($form->getData());*/
        }
        return $this->renderForm('consultation/commencer.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consulation $consulation, ConsulationRepository $consulationRepository): Response
    {
        $medecin = $this->getUser();

        if ($consulation->getMedecin() !== $medecin) {
            throw new AccessDeniedException('Vous n êtes pas autorisé à accéder à cette page');
        }

        $form = $this->createForm(ConsulationType::class, $consulation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $consulationRepository->save($consulation, true);

            return $this->redirectToRoute('app_patient_show_consu', ['id' => $consulation->getPatients()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('consultation/edit.html.twig', [
            'consulation' => $consulation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_delete', methods: ['POST'])]
    public function delete(Request $request, Consulation $consulation, ConsulationRepository $consulationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $consulation->getId(), $request->request->get('_token'))) {
            $consulationRepository->remove($consulation, true);
        }

        return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
    }
}