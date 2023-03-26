<?php

namespace App\Controller;

use App\Entity\Diagnostique;
use App\Entity\Dossier;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Form\DiagnostiqueType;
use App\Repository\DiagnostiqueRepository;
use App\Form\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PatientRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


#[Route('/diagnostique')]
class DiagnostiqueController extends AbstractController
{
    #[Route('/', name: 'app_diagnostique_index', methods: ['GET'])]
    public function index(DiagnostiqueRepository $diagnostiqueRepository): Response
    {

        return $this->render('diagnostique/index.html.twig', [
            'diagnostiques' => $diagnostiqueRepository->findAll(),
        ]);
    }



    #[Route('/new/{id_patient}', name: 'app_diagnostique_new', methods: ['GET', 'POST'])]
    public function new(Request $request,$id_patient,DiagnostiqueRepository $diagnostiqueRepository, PatientRepository $patientRepository): Response
    {
        $diagnostique = new Diagnostique();
        $form = $this->createForm(DiagnostiqueType::class, $diagnostique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $patient = $patientRepository->findOneBy(['id' => $id_patient]);
            
           
            $dossiers = $patient->getDossiers();
            $dossier = $dossiers->first(); // or loop over $dossiers to find the right one
            $diagnostique = $form->getData();
            $diagnostique->setPatient($patient);
            $diagnostique->setDossiers($dossier);
           
            $diagnostiqueRepository->save($diagnostique, true);
               
            return $this->redirectToRoute('app_patient_show_consu', ['id' => $diagnostique->getPatient()->getId()], Response::HTTP_SEE_OTHER);
           
        }

        return $this->renderForm('diagnostique/new.html.twig', [
            'diagnostique' => $diagnostique,
            
            'form' => $form,
        ]);
    }

       #[Route('/generate-pdf', name:'generatePDF', methods:['GET'])]
    public function generatePDF(DiagnostiqueRepository $diagnostiqueRepository)
 {
    
     // Configure Dompdf according to your needs
     $pdfOptions = new Options();
     $pdfOptions->set('defaultFont', 'Arial');
 
     // Instantiate Dompdf with our options
     $dompdf = new Dompdf($pdfOptions);
     // Retrieve the HTML generated in our twig file
     $html = $this->renderView('dossier/pdf.html.twig', [
         'diagnostiques' => $diagnostiqueRepository->findAll(),
     ]);
 
     // Load HTML to Dompdf
     $dompdf->loadHtml($html);
     // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
     $dompdf->setPaper('A4', 'portrait');
 
     // Render the HTML as PDF
     $dompdf->render();
     // Output the generated PDF to Browser (inline view)
     $dompdf->stream("ListeDesDiagnostiques.pdf", [
         "diagnostiques" => true
     ]);
 }

    #[Route('/{id}', name: 'app_diagnostique_show', methods: ['GET'])]
    public function show(Diagnostique $diagnostique): Response
    {
        return $this->render('diagnostique/show.html.twig', [
            'diagnostique' => $diagnostique,
        ]);
    }

    #[Route('/{id_patient}/edit', name: 'app_diagnostique_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id_patient,Diagnostique $diagnostique, PatientRepository $patientRepository,DiagnostiqueRepository $diagnostiqueRepository): Response
    {
        $form = $this->createForm(DiagnostiqueType::class, $diagnostique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $patient = $patientRepository->findOneBy(['id' => $id_patient]);
            $dossiers = $patient->getDossiers();
            $dossier = $dossiers->first();
            $diagnostique = $form->getData();
          
            $diagnostique->setPatient($patient);
            $diagnostique->setDossiers($dossier);
            $diagnostiqueRepository->save($diagnostique, true);

            

            return $this->redirectToRoute('app_patient_show_consu', ['id' => $diagnostique->getPatient()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diagnostique/edit.html.twig', [
            'diagnostique' => $diagnostique,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_diagnostique_delete', methods: ['POST'])]
    public function delete(Request $request, Diagnostique $diagnostique, DiagnostiqueRepository $diagnostiqueRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$diagnostique->getId(), $request->request->get('_token'))) {
            $diagnostiqueRepository->remove($diagnostique, true);
        }

        return $this->redirectToRoute('app_diagnostique_index', [], Response::HTTP_SEE_OTHER);
    }
}