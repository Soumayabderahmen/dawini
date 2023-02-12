<?php

namespace App\Controller;
use App\Entity\Images;
use App\Entity\Specialites;
use App\Form\SpecialitesType;
use App\Repository\SpecialitesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/specialites')]
class SpecialitesController extends AbstractController
{
    #[Route('/', name: 'app_specialites_index', methods: ['GET'])]
    public function index(SpecialitesRepository $specialitesRepository): Response
    {
        return $this->render('specialites/index.html.twig', [
            'specialites' => $specialitesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_specialites_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SpecialitesRepository $specialitesRepository): Response
    {
        $specialite = new Specialites();
        $form = $this->createForm(SpecialitesType::class, $specialite);
        $form->handleRequest($request);

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
                $specialite->addImage($img);
            }
            $specialitesRepository->save($specialite, true);

            return $this->redirectToRoute('app_specialites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('specialites/new.html.twig', [
            'specialite' => $specialite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_specialites_show', methods: ['GET'])]
    public function show(Specialites $specialite): Response
    {
        return $this->render('specialites/show.html.twig', [
            'specialite' => $specialite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_specialites_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Specialites $specialite, SpecialitesRepository $specialitesRepository): Response
    {
        $form = $this->createForm(SpecialitesType::class, $specialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                $specialite->addImage($img);
            }
            $specialitesRepository->save($specialite, true);

            return $this->redirectToRoute('app_specialites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('specialites/edit.html.twig', [
            'specialite' => $specialite,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_specialites_delete', methods: ['POST'])]
    public function delete(Request $request, Specialites $specialite, SpecialitesRepository $specialitesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$specialite->getId(), $request->request->get('_token'))) {
            $specialitesRepository->remove($specialite, true);
        }

        return $this->redirectToRoute('app_specialites_index', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('image/delete/{id}', name: 'image_delete_specialite')]
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $specialite = $images->getSpecialite($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no specialite with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_specialites_edit', ['id' => $specialite->getId()]);
    }
}
