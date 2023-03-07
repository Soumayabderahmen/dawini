<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\ReplaySujet;
use App\Form\ReplaySujetType;
use App\Repository\ReplaySujetRepository;
use App\Repository\SujetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/replay/sujet')]
class ReplaySujetController extends AbstractController
{
    #[Route('/', name: 'app_replay_sujet_index', methods: ['GET'])]
    public function index(ReplaySujetRepository $replaySujetRepository): Response
    {
        return $this->render('replay_sujet/index.html.twig', [
            'replay_sujets' => $replaySujetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_replay_sujet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReplaySujetRepository $replaySujetRepository): Response
    {
        $replaySujet = new ReplaySujet();
        $form = $this->createForm(ReplaySujetType::class, $replaySujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $replaySujetRepository->save($replaySujet, true);

            return $this->redirectToRoute('app_replay_sujet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('replay_sujet/new.html.twig', [
            'replay_sujet' => $replaySujet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_replay_sujet_show', methods: ['GET'])]
    public function show(ReplaySujet $replaySujet): Response
    {
        return $this->render('replay_sujet/show.html.twig', [
            'replay_sujet' => $replaySujet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_replay_sujet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReplaySujet $replaySujet, $id,ReplaySujetRepository $replaySujetRepository,SujetRepository $sujet): Response
    { 
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($replaySujet->getUtilisateur() != $this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        $form = $this->createForm(ReplaySujetType::class, $replaySujet);
        $form->handleRequest($request);
        $replay=$replaySujet->getSujet($id);
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
               $replaySujet->addImage($img);
              }
            $replaySujetRepository->save($replaySujet, true);

            return $this->redirectToRoute('app_sujet_avoir',['id' => $replay->getId()
        ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('replay_sujet/edit.html.twig', [
            'replay_sujet' => $replaySujet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_replay_sujet_delete', methods: ['POST'])]
    public function delete(Request $request, ReplaySujet $replaySujet, $id,ReplaySujetRepository $replaySujetRepository): Response

    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($replaySujet->getUtilisateur() != $this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        
        $replay=$replaySujet->getSujet($id);

        if ($this->isCsrfTokenValid('delete'.$replaySujet->getId(), $request->request->get('_token'))) {
            $replaySujetRepository->remove($replaySujet, true);
        }

        return $this->redirectToRoute('app_sujet_avoir', ['id' => $replay->getId()], Response::HTTP_SEE_OTHER);
    }
    #[Route('image/delete/{id}', name: 'image_delete_replay')]
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $replaySujet = $images->getReplaySujet($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no specialite with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_replay_sujet_edit', ['id' => $replaySujet->getId()]);
    }
}
