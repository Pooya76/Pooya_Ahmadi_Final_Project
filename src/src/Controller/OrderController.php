<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderController extends AbstractController
{
    private $user;
    #[Route(
        path: '/{_locale}/product/order/{id}',
        name: 'app_product_order',
        requirements: ['_locale' => 'en|fa'],
        defaults: ['_locale' => 'en']
    )]
    public function index(Request $request, ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage): Response
    {
        if (!is_null($tokenStorage->getToken()))
            $this->user = $tokenStorage->getToken()->getUser();

        $order = new Order();

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $order = $form->getData();
            $order->setPersonName($this->user->getUserIdentifier());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($order);
            $entityManager->flush();
            return new Response('Order added successfully');
        }
        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }
}
