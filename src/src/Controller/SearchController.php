<?php

namespace App\Controller;

use App\Search\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, SearchService $searchService): Response
    {
        $query = $request->query->get('query');
        $products = $searchService->searchHotel($query);
        return $this->render('search/index.html.twig', [
            'products' => $products,
        ]);
    }
}
