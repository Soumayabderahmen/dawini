<?php

namespace App\Controller;

use App\Entity\ReplaySujet;
use App\Entity\Sujet;
use App\Repository\PatientRepository;
use App\Repository\ReplaySujetRepository;
use App\Repository\SpecialitesRepository;
use App\Repository\SujetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ForumController extends AbstractController
{
    
        #[Route('/forumAdmin', name: 'app_forum_index', methods: ['GET'])]
    public function forum(SujetRepository $sujet,ReplaySujetRepository $replay,CacheInterface $cache ,Request $request,SpecialitesRepository $specialitesRepository): Response
    {           $filters = $request->get("specialites");
                $total = $sujet->getTotalAnnonces($filters);
                if($request->get('ajax')){
                    return new JsonResponse([
                        'content' => $this->renderView('forum/dashborad.html.twig', compact('total'))
                    ]);
                }
                $specialites = $cache->get('categories_list', function(ItemInterface $item) use($specialitesRepository){
                    $item->expiresAfter(3600);
        
                    return $specialitesRepository->findAll();
                });
        return $this->render('forum/dashborad.html.twig', [
            'sujets' => $sujet->findAll([],['id'=>'desc']),
            'specialites'=>$specialitesRepository->findAll(),
           

        ]);
    }

    #[Route('voir/{id}/showForum', name: 'app_sujet_avoir_show', methods: ['GET', 'POST'])]

    public function voirForum($id, Sujet $sujet,ReplaySujetRepository $replay)
    {
        
      
        $countreponse = count($replay->findBySujet($id));
        
            $replay->findBySujet($sujet);
            

        return $this->render('forum/show.html.twig', [
            'sujets' => $sujet,
            'id' => $id,
            'replays' => $replay->findBySujet($sujet), 
           
            'countreponse'=>$countreponse,
            


        ]);

    }
    #[Route('/{id}/delete', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(Request $request, ReplaySujet $replaySujet, $id,ReplaySujetRepository $replaySujetRepository): Response
    {        $replay=$replaySujet->getSujet($id);

        if ($this->isCsrfTokenValid('delete'.$replaySujet->getId(), $request->request->get('_token'))) {
            $replaySujetRepository->remove($replaySujet, true);
        }

        return $this->redirectToRoute('app_sujet_avoir_show', ['id' => $replay->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/profil/{id}', name: 'app_forum_profil', methods: ['GET'])]
    public function profil(PatientRepository $patientRepository ,$id,ReplaySujetRepository $replay ,SujetRepository $sujet ): Response
   
    {     $idUser=$this->getUser();
        $patient = $patientRepository->findById($id);
        $PatientSelectioner = $patientRepository->findOneBy(['id' => $idUser]);
        
        $countreponse = count($replay->findByUtilisateur($PatientSelectioner));
        $countquestion = count($sujet->findByUtilisateur($PatientSelectioner));
        $test=$sujet->findByUtilisateur($PatientSelectioner,['id'=>'desc']);
        dump($PatientSelectioner);

        $sujets=$sujet->findByUtilisateur($PatientSelectioner);
        $replays=$replay->findByUtilisateur($PatientSelectioner);

        return $this->render('forum/UserProfil.html.twig', [
            'id'=>$id,
            'patients'=>$PatientSelectioner,
             'sujets'=>$sujets,
             'test'=>$test,
             'replays'=>$replays,
           'countreponse'=>$countreponse,
           'countquestion'=>$countquestion,
           'users' => $PatientSelectioner

        ]);
    }
   
}