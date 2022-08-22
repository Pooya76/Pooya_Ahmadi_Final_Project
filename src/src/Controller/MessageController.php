<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{

    #[Route(
        path: '/{_locale}/message',
        name: 'app_message',
        requirements: ['_locale' => 'en|fa'],
        defaults: ['_locale' => 'en']
    )]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $message = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            return new Response('Message added successfully');
        }
        return $this->render('message/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
