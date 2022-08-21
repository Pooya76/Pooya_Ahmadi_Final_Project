<?php

namespace App\Search;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class SearchService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function searchHotel($input){
        $hotelRepository = $this->entityManager->getRepository(Product::class);
        return $hotelRepository->createQueryBuilder('product')->where('product.name like :q')->setParameter('q', '%'. $input . '%')->getQuery()->getResult();
    }
}