<?php

namespace App\Controller;
use App\Form\ContactType;
use App\Entity\Article;
use App\Entity\ArticleFavorie;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Repository\SpecialitesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\SendMailService;
use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisType;
use App\Entity\Medecin;
use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use App\Repository\MedecinRepository;
use App\Repository\PatientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
   
    #[Route('/', name: 'app_front')]
    public function index(MedecinRepository  $userRepository,ArticleRepository $articleRepository): Response
    {
        
        return $this->render('home.html.twig', [
            'medecins' => $userRepository->findAll(),
            'users' => $userRepository->findAll(),
            'articles' => $articleRepository->findAll(),
            
        
        ]);
    }

    #[Route('/profil/{id}', name: 'app_profil')]
    public function avis(MedecinRepository $medecinRepository, User $user,PatientRepository $patientRepository,Request $request,$id,AvisRepository $avisRepository): Response
    {  
        $medecin = $medecinRepository->findById($id);
        $medecinSelectioner = $medecinRepository->findOneBy(['id' => $id]);

       $iduser=$this->getUser();
      
        $avi = new Avis();
        $avis=$avisRepository->findByMedecin($id);
        $countavis = count($avisRepository->findByMedecin($id));
        $formAvis = $this->createForm(AvisType::class, $avi);
        $formAvis->handleRequest($request);
        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $medecinSelectioner = $medecinRepository->findOneBy(['id' => $id]);
            $patientConnecter = $patientRepository->findOneBy(['id' => $iduser]);

            $avi->setDate(new \DateTime('now'));
            $avi->setStatut("Activer");
            $avi->setMedecin($medecinSelectioner);
            $avi->setPatient($patientConnecter);
            $avisRepository->save($avi, true);
            
            return $this->redirectToRoute('app_profil' ,['id'=>$id]);
        }
        return $this->renderForm('front/avis.html.twig', [
            'formAvis' => $formAvis,
            'countavis'=>$countavis,
            'avis'=>$avis,
            'medecin'=>$medecin,
        ]);
    }
    
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, SendMailService $mail)
    {
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $context = [
               
                'mail' => $contact->get('email')->getData(),
                'sujet' => $contact->get('sujet')->getData(),
                'message' => $contact->get('message')->getData(),
            ];
            $mail->send(
                $contact->get('email')->getData(),
                'vous@domaine.fr',
                'Contact depuis le site Dawini',
                'contact',
                $context
            );

            $this->addFlash('message', 'Votre Message a été envoyer');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contactUs.html.twig', [
            'form' => $form->createView()
        ]);
    }
    public function getArticlesInFavorites($idsSelectedArticles)
    {
        if ($this->getUser()) {

            $user = $this->getUser();

            $favorites = $this->getDoctrine()->getManager()->getRepository(ArticleFavorie::class)
            ->findBy(array('user' => $user));
            $ids = array_map(function ($entity) {
                return $entity->getArticle()->getId();
            }, $favorites); // get ids of dishes in favorite


        } else {

            $ids = array(); // if user is not AUTHENTICATED_REMEMBERED show ids are null
        }

        return $ids;
    }

    #[Route('/blog', name: 'app_blog' , methods:['GET'])]
    public function blog(Request $request,ArticleRepository $articleRepository ,PaginatorInterface $paginator, SpecialitesRepository $specialitiesRepository): Response
    {
        $article=$articleRepository->findAll();
        $specialities=$specialitiesRepository->findAll();
        
        $article = $paginator->paginate(
            $article, // Requête contenant les données à paginer 
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            1 //Nombre de résultats par page
        );
        $idsSelectedArticles = [];
        foreach($article as $art){
            $idsSelectedArticles[] = $art->getId();
        }

        $idsArticlesInFavorites = $this->getArticlesInFavorites($idsSelectedArticles);

        return $this->render('front/article.html.twig', [
            'articles' => $article,
            'specialities'=>$specialities,
            'idsArticlesInFavorites'=> $idsArticlesInFavorites
            

        ]);
    }

    #[Route('/blogDetails/{id}', name: 'app_blog_details' , methods: ['GET', 'POST'])]
    public function blogDetails(ArticleRepository $articleRepository,SpecialitesRepository $specialitiesRepository,$id,Article $article,Request $request,CommentaireRepository $commentaireRepository,PatientRepository $patientRepository): Response
    {     
        $iduser=$this->getUser();
        $UserConnecter=$patientRepository->findOneBy(['id'=> $iduser]);

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        $commentaireRepository->findByArticle($article);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $commentaire->setArticle($article);
            $commentaire->setUtilisateur($UserConnecter);

            $commentaire->setDate(new \DateTime());
           $em->persist($commentaire);
           $em->flush();
            return $this->redirectToRoute('app_blog_details', ['id' => $id]);
            dump($commentaire);
        }
       // $count = $specialitiesRepository->article->count();
     

        return $this->renderForm('front/blog-details.html.twig', [
            'articles' => $articleRepository->findById($id),
            'article'=>$articleRepository->findBy([],['id'=>'desc']),
            'commentaires'=>$commentaireRepository->findByArticle($article),
            'specialities'=>$specialitiesRepository->findALL(),
            //'specialities'=>$articleRepository->findBySpecialites($id),
            'form' => $form,

        ]);
    }
     
    #[Route('/article/favories/add-delete/{id}', name: 'article_favorie')]
    public function articleFavories(Request $request, Article $article, ManagerRegistry $doctrine)
    {
        $entityManger = $doctrine->getManager();

        $user = $this->getUser();
        $articleFavoriesExiste = $entityManger->getRepository(ArticleFavorie::class)
        ->findOneBy(array('article'=> $article, 'user'=> $user));
        if($articleFavoriesExiste){
            $entityManger->remove($articleFavoriesExiste);
            $entityManger->flush();
        }
        else{
            $articleFavorie = new ArticleFavorie();
            $articleFavorie->setUser($user);
            $articleFavorie->setArticle($article);
            $entityManger->persist($articleFavorie);
            $entityManger->flush();
        }

       return new JsonResponse();
    }
    
}

