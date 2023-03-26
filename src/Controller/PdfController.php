<?php

namespace App\Controller;
use App\Entity\Ordonnance;
use Dompdf\Dompdf;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    #[Route('/pdf/generator/{id}', name: 'app_pdf_generator')]
    public function index(Ordonnance $ordonnance): Response
    {

        // return $this->render('pdf_generator/index.html.twig', [
        //     'controller_name' => 'PdfGeneratorController',
        // ]);

        $data = [
            //'imageSrc'  => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/public/img/profile.png'),
            'date'         => $ordonnance->getDate()->format('Y-m-d H:i'),
            'Discreption'      => $ordonnance->getDescription(),
            'photo' => $ordonnance->getImage(),
            'patient'=>$ordonnance->getConsulation()->getPatients()->getNom().'  '.$ordonnance->getConsulation()->getPatients()->getPrenom(),
            'medecin'=>$ordonnance->getConsulation()->getMedecin()->getNom().'  '.$ordonnance->getConsulation()->getMedecin()->getPrenom(),

            'urlPhoto' => 'http://127.0.0.1:8000/ordannaces/'.$ordonnance->getImage()
        ];
        $html =  $this->renderView('pdf_generator/index.html.twig', $data);
        $dompdf = new Dompdf(array('enable_remote' => true));
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        return new Response (
            $dompdf->stream('resume', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }
}
