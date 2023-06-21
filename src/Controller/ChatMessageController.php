<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ChatMessage;
use App\Form\ChatMessageFormType;
use App\Repository\ChatMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChatMessageController extends AbstractController
{
    public function __construct(
        private ChatMessageRepository $chatMessageRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $chatMessages = $this->chatMessageRepository->findAll();

        return $this->render('index.html.twig', [
            'chatMessages' => $chatMessages,
        ]);
    }

    #[Route('/message/new', name: 'message_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $chatMessage = new ChatMessage();

        $form = $this->createForm(ChatMessageFormType::class, $chatMessage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chatMessage = $form->getData();
            $user = $this->getUser();
            $chatMessage->setAuthor($user);
            $this->entityManager->persist($chatMessage);
            $this->entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('chatMessage/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/message/{id}/edit', name: 'message_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ChatMessage $chatMessage): Response
    {
        $form = $this->createForm(ChatMessageFormType::class, $chatMessage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'article.action.edit.success');

            return $this->redirectToRoute('home');
        }

        return $this->render('chatMessage/edit.html.twig', [
            'chatMessage' => $chatMessage,
            'edit_form' => $form,
        ]);
    }

    #[Route('/message/{id}/delete', name: 'message_delete')]
    public function delete(Request $request, ChatMessage $chatMessage): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chatMessage->getId(), $request->request->get('_token'))) {
            $this->chatMessageRepository->remove($chatMessage);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }
}
