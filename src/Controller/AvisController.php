<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Form\StatutType;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/avis')]
class AvisController extends AbstractController
{
    #[Route('/', name: 'app_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        $medecin = $this->getUser();
        return $this->render('avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
            //'avis' => $avisRepository->findByMedecin($medecin),

        ]);
    }

    #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AvisRepository $avisRepository): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avi->setDate(new \DateTime('now'));
            
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }
    ///Mobile
    #[Route('/All', name: 'app_avis_liste')]
    public function ListeAvis(AvisRepository $avis, SerializerInterface $serializer)
    {
        $avis = $avis->findAll();
        $avisNormailize = $serializer->serialize($avis, 'json', ['groups' => "avis"]);
    
        $json = json_encode($avisNormailize);
        return  new response($json);
    }

    #[Route('/AvisJson/{id}', name: 'app_avis_seule')]
public function adminId($id,AvisRepository $avis, SerializerInterface $serializer)
{
    $avis = $avis->find($id);
    $avisNormailize = $serializer->serialize($avis, 'json', ['groups' => "avis"]);

    $json = json_encode($avisNormailize);
    return  new response($json);
}

    #[Route('/add/AvisJson', name: 'app_avis_new_json')]
    public function addAvisJson(Request $request, NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $avis = new Avis();
        $avis->setText($request->get('text'));
        $avis->setNote($request->get('note'));
        $avis->setStatut($request->get('statut'));
        $avis->setDate(new \DateTime());
        $avis->setPatient($request->get('patient'));
        $avis->setMedecin($request->get('medecin'));
        
        
        $em->persist($avis);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($avis,'json',['groups'=>'avis']);
        return new Response(json_encode($jsonContent));
    
       
    }


    #[Route('/edit/{id}/AvisJson', name: 'app_avis_edit_json')]
    public function editAvisJson(Request $request,$id,NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $avis=$em->getRepository(Avis::class)->find($id);
        $avis->setText($request->get('text'));
        $avis->setNote($request->get('note'));
        $avis->setStatut($request->get('statut'));
        $avis->setDate(new \DateTime());
        $avis->setPatient($request->get('patient'));
        $avis->setMedecin($request->get('medecin'));
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($avis,'json',['groups'=>'avis']);
        return new Response(json_encode($jsonContent));
    
       
    }
    #[Route('/delete/AvisJson/{id}', name: 'app_avis_delete_seule')]
    public function deleteAdminJson($id,AvisRepository $avis, NormalizerInterface $normalizerInterface,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $avis=$em->getRepository(Avis::class)->find($id);
        $em->remove($avis);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($avis,'json',['groups'=>'medecin']);
        return  new Response("avis deleted successfully". json_encode($jsonContent));
    }







    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        $form = $this->createForm(StatutType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->request->get('_token'))) {
            $avisRepository->remove($avi, true);
        }

        return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
    }
}
