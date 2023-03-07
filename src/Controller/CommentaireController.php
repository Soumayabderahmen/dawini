<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository ): Response
    {    
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
           

        ]);
    }

    
    //Mobile
    #[Route('/All', name: 'app_commentaires_liste')]
    public function ListeCommentaire(CommentaireRepository $commentaire, SerializerInterface $serializer)
    {
        $commentaire = $commentaire->findAll();
        $commentaireNormailize = $serializer->serialize($commentaire, 'json', ['groups' => "commentaires"]);

        $json = json_encode($commentaireNormailize);
        return  new response($json);
    }

    #[Route('/commentaireJson/{id}', name: 'app_commentaire_seule')]
    public function artcileId($id,CommentaireRepository $commentaire, SerializerInterface $serializer)
    {
        $commentaire = $commentaire->find($id);
        $commentaireNormailize = $serializer->serialize($commentaire, 'json', ['groups' => "commentaires"]);
    
        $json = json_encode($commentaireNormailize);
        return  new response($json);
    }
    #[Route('/add/commentaireJson', name: 'app_commentaire_new_json')]
    public function addcommentaireJson(Request $request, NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $commentaire = new commentaire();
        $commentaire->setDate(new \DateTime());
        $commentaire->setMessage($request->get('message'));
        
        
        $em->persist($commentaire);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($commentaire,'json',['groups'=>'commentaires']);
        return new Response(json_encode($jsonContent)); 
    
    }
    #[Route('/edit/{id}/commentaireJson', name: 'app_commentaire_edit_json')]
    public function editCommentaireJson(Request $request, $id,NormalizerInterface $normalizerInterface): Response
    {   
        $em=$this->getDoctrine()->getManager();
        $commentaire=$em->getRepository(Commentaire::class)->find($id);
       
       
        $commentaire->setDate(new \DateTime());
        $commentaire->setMessage($request->get('message'));
    
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($commentaire,'json',['groups'=>'commentaires']);
        return new Response(json_encode($jsonContent));
 
    }
    #[Route('/delete/commentaireJson/{id}', name: 'app_commentaire_delete_seule')]
    public function deleteassistantJson($id,CommentaireRepository $commentaire, NormalizerInterface $normalizerInterface,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $commentaire=$em->getRepository(Commentaire::class)->find($id);
        $em->remove($commentaire);
        $em->flush();
        $jsonContent=$normalizerInterface->normalize($commentaire,'json',['groups'=>'commentaires']);
        return  new Response("commentaire deleted successfully". json_encode($jsonContent));
    }





    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {   

        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
            
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaireRepository->save($commentaire, true);

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaireRepository->remove($commentaire, true);
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }
}