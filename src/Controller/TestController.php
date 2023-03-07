<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SujetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
}
