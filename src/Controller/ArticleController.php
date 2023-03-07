<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleLike;
use App\Entity\Images;
use App\Entity\Medecin;
use App\Entity\Specialites;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Repository\MedecinRepository;
use App\Repository\SpecialitesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#lena route lkol men fou9 bch tkoun fama kelmet article w koll page bch todekhlelha bch tetzed route mtaaa l page athyka l route mere #
#[Route('/article')]
class ArticleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        #permet de faire le crud
        
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            
        ]);
    }

    #[Route('/mesarticles', name: 'app_article_mine', methods: ['GET'])]
    public function article(ArticleRepository $articleRepository): Response
    {   
        return $this->render('medecin/blog/mesarticles.html.twig', [
            'articles' => $articleRepository->findAll(),
            

            
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {    $iduser=$this->getUser();
          $specialte=$iduser->getSpecialites();
          #inastancier un nouvelle objet de type article
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        #permet de lier le formulaire a la requette https recue par le controlleur 
        $form->handleRequest($request);
#la methode  vérifie si la formulaire a été soumis et validé 
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
                 $article->addImage($img);
             }
             $article->setDate(new \DateTime());
             $article->setMedecin($iduser);
             $article->setSpecialites($specialte);

                     $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_mine', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('medecin/blog/add-blog.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    
    //Mobile
        #[Route('/All', name: 'app_articles_liste')]
        public function ListeArticle(ArticleRepository $article, SerializerInterface $serializer)
        {
            $article = $article->findAll([],['nom'=>'desc']);
            $articleNormailize = $serializer->serialize($article, 'json', ['groups' => "articles"]);
    
            $json = json_encode($articleNormailize);
            return  new response($json);
        }
      
        #[Route('/articleJson/{id}', name: 'app_article_seule')]
        public function artcileId($id,ArticleRepository $article, SerializerInterface $serializer)
        {
            $article = $article->find($id);
            $articleNormailize = $serializer->serialize($article, 'json', ['groups' => "articles"]);
        
            $json = json_encode($articleNormailize);
            return  new response($json);
        }

        #[Route('/add/articleJson', name: 'app_article_new_json')]
        public function addarticleJson(Request $request, NormalizerInterface $normalizerInterface): Response
        {   
            $em=$this->getDoctrine()->getManager();
            $article = new article();
            $article->setNom($request->get('nom'));
            $article->setDescription($request->get('description'));
            $article->setDate(new \DateTime());
          
            
            
            $em->persist($article);
            $em->flush();
            $jsonContent=$normalizerInterface->normalize($article,'json',['groups'=>'articles']);
            return new Response(json_encode($jsonContent));
        
           
        }

        #[Route('/edit/{id}/articleJson', name: 'app_article_edit_json')]
        public function editArticleJson(Request $request, $id,NormalizerInterface $normalizerInterface): Response
        {   
            $em=$this->getDoctrine()->getManager();
            $article=$em->getRepository(Article::class)->find($id);
           
           
            $article->setNom($request->get('nom'));
            $article->setDescription($request->get('description'));
            $article->setDate(new \DateTime());
           
        
            $em->flush();
            $jsonContent=$normalizerInterface->normalize($article,'json',['groups'=>'articles']);
            return new Response(json_encode($jsonContent));
  
        
        }
        #[Route('/delete/articleJson/{id}', name: 'app_article_delete_seule')]
        public function deleteArticleJson($id,ArticleRepository $article, NormalizerInterface $normalizerInterface,Request $request)
        {
            $em=$this->getDoctrine()->getManager();
            $article=$em->getRepository(Article::class)->find($id);
            $em->remove($article);
            $em->flush();
            $jsonContent=$normalizerInterface->normalize($article,'json',['groups'=>'articles']);
            return  new Response("article deleted successfully". json_encode($jsonContent));
        }
    








    #[Route('/show/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article ,CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentaires'=>$commentaireRepository->findByArticle($article),
        ]);
    }

    #[Route('article/{id}', name: 'app_article_medecin', methods: ['GET'])]
    public function showArticle(ArticleRepository $articleRepository,Article $article ,CommentaireRepository $commentaireRepository,SpecialitesRepository $specialitiesRepository,$id): Response
    {
        return $this->render('medecin/blog/show-blog.html.twig', [
            'article' => $article,
            'commentaires'=>$commentaireRepository->findByArticle($article),
            'specialities'=>$articleRepository->findBySpecialites($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
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
                  $article->addImage($img);
              }
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_mine', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('medecin/blog/edit-blog.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_article_mine', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('image/delete/{id}', name: 'image_delete_article')]
    public function deleteImages($id)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getDoctrine()->getRepository(Images::class);
        $images = $images->find($id);
        $article = $images->getAticles($id);
        if (!$images) {
            throw $this->createNotFoundException(
                'There are no article with the following id: ' . $id
            );
        }
        $em->remove($images);
        $em->flush();
        return $this->redirectToRoute('app_article_edit', ['id' => $article->getId()]);
    }
    #[Route('/article/{id}/like', name: 'article_like', methods: ['GET', 'POST'])]
    public function like(Article $article): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Vous devez être connecté pour liker un article.'], 403);
        }

        $articleLike = $entityManager->getRepository(ArticleLike::class)->findOneBy([
            'article' => $article,
            'user' => $user,
        ]);

        if (!$articleLike) {
            $articleLike = new ArticleLike();
            $articleLike->setArticle($article)
                ->setUser($user);
        }

        $articleLike->setValue(ArticleLike::LIKE);
        $entityManager->persist($articleLike);
        $entityManager->flush();

        return $this->json(['count' => $article->getLikesCount()]);
    }
 
#[Route('/article/{id}/dislike', name: 'article_dislike', methods: ['GET', 'POST'])]
public function dislike(Article $article): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->getUser();

    if (!$user) {
        return $this->json(['error' => 'Vous devez être connecté pour disliker un article.'], 403);
    }

    $articleLike = $entityManager->getRepository(ArticleLike::class)->findOneBy([
        'article' => $article,
        'user' => $user,
    ]);

    if (!$articleLike) {
        $articleLike = new ArticleLike();
        $articleLike->setArticle($article)
            ->setUser($user);
    }

    $articleLike->setValue(ArticleLike::DISLIKE);
    $entityManager->persist($articleLike);
    $entityManager->flush();

    return $this->json(['count' => $article->getLikesCount()]);
}


#[Route('/article/{id}/unlike', name: 'article_unlike', methods: ['GET', 'POST'])]
public function unlike(Article $article): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->getUser();

    if (!$user) {
        return $this->json(['error' => 'Vous devez être connecté pour unliker un article.'], 403);
    }

    $articleLike = $entityManager->getRepository(ArticleLike::class)->findOneBy([
        'article' => $article,
        'user' => $user,
    ]);

    if ($articleLike) {
        $entityManager->remove($articleLike);
        $entityManager->flush();
    }

    return $this->json(['count' => $article->getLikesCount()]);
}


#[Route('/article/{id}/undislike', name: 'article_undislike', methods: ['GET', 'POST'])]
public function undislike(Article $article): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->getUser();

    if (!$user) {
        return $this->json(['error' => 'Vous devez être connecté pour undisliker un article.'], 403);
    }

    $articleLike = $entityManager->getRepository(ArticleLike::class)->findOneBy([
        'article' => $article,
        'user' => $user,
    ]);

    if ($articleLike) {
        $entityManager->remove($articleLike);
        $entityManager->flush();
    }

    return $this->json(['count' => $article->getLikesCount()]);
}


}