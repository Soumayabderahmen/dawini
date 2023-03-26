<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SujetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Consulation;
use App\Entity\Ordonnance;
use App\Repository\ConsulationRepository;
use App\Repository\ConsultationRepository;
use App\Repository\DossierRepository;
use App\Repository\UserRepository;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/search_ajax', name: 'app_rest_search', methods: ['GET'])]
public function search(Request $request, SujetRepository $repo): Response
{
  
    $keyword = $request->query->get('keyword');
   
    $data =
    
    $repo->createQueryBuilder('s')
    ->andWhere('s.title LIKE :searchTerm')
   

    ->setParameter('searchTerm', '%'.$keyword.'%')
   
    ->getQuery()
    ->execute();
    /*$repo->createQueryBuilder('c')->andWhere('c.date LIKE :key')->andWhere('c.patients LIKE :pat')
    ->setParameter('pat', $patient)
    ->setParameter('key', '%' . $keyword . '%')
    ->getQuery()->getResult();*/
    $dataR = array();
    foreach ($data as $item) {
        array_push($dataR, [
            'id' => $item->getId(), 
            'date' => $item->getDate()->format('Y/d/m'), 
            'title' => $item->getTitle(),
           'message' => $item->getMessage()
        ]);
    }
    return new JsonResponse(['data' => $dataR], 200);
}
#[Route('/search_ajax', name: 'app_rest_search', methods: ['GET'])]
    public function searchA(Request $request, ConsulationRepository $repo, UserRepository $userRepository): Response
    {
        $keyword = $request->query->get('keyword');
        $patient = $request->query->get('patient');
        $patient = $userRepository->find(intval($patient));
        $data =

            $repo->createQueryBuilder('c')
            ->andWhere('c.date LIKE :searchTerm')
            ->andWhere('c.patients = :patient')
            ->setParameter('searchTerm', '%' . $keyword . '%')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->execute();
        /*$repo->createQueryBuilder('c')->andWhere('c.date LIKE :key')->andWhere('c.patients LIKE :pat')
    ->setParameter('pat', $patient)
    ->setParameter('key', '%' . $keyword . '%')
    ->getQuery()->getResult();*/
        $dataR = array();
        foreach ($data as $item) {
            array_push($dataR, [
                'id' => $item->getId(),
                'date' => $item->getDate()->format('Y-d-m H:i'),
                'patient' => $item->getPatients()->getNom() . ' ' . $item->getPatients()->getPrenom(),
                'debut' => $item->getHeureDebut()->format('H:i'),
                'fin' => $item->getHeureDebut()->format('H:i')
            ]);
        }
        return new JsonResponse(['data' => $dataR], 200);
    }
    #[Route('/search_ajaxD', name: 'app_rest_searchDossier', methods: ['GET'])]
    public function searchDossier(Request $request, DossierRepository $repo, UserRepository $userRepository): Response
    {
        $keyword = $request->query->get('keyword');
        $patient = $request->query->get('patient');
        $patient = $userRepository->find(intval($patient));
        $data =

            $repo->createQueryBuilder('d')
            ->andWhere('d.numero LIKE :searchTerm')
            ->andWhere('d.patients = :patient')
            ->setParameter('searchTerm', '%' . $keyword . '%')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->execute();
        /*$repo->createQueryBuilder('c')->andWhere('c.date LIKE :key')->andWhere('c.patients LIKE :pat')
    ->setParameter('pat', $patient)
    ->setParameter('key', '%' . $keyword . '%')
    ->getQuery()->getResult();*/
        $dataR = array();
        foreach ($data as $item) {
            array_push($dataR, [
                'id' => $item->getId(),
                'numero' => $item->getNumero(),
                'code_APCI' => $item->getCodeAPCI(),
                'description' => $item->$this->getDescription()
            ]);
        }
        return new JsonResponse(['data' => $dataR], 200);
    }
}
