<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ChatMessage;
use App\Form\ChatMessageFormHandler;
use App\Form\ChatMessageFormType;
use App\Repository\ChatMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChatMessageController extends AbstractController
{
    public function __construct(
        private ChatMessageRepository $chatMessageRepository,
        private EntityManagerInterface $entityManager,
        private ChatMessageFormHandler $chatMessageFormHandler,
        private HubInterface $hub,
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $chatMessage = $this->chatMessageFormHandler->handleNewChatMessageForm($form);
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());

                    return $this->redirectToRoute('home');
                }

                $this->chatMessageRepository->save($chatMessage, true);

                $update = new Update(
                    'chat',
                    json_encode([
                        'text' => $chatMessage->getText(),
                        'author' => $chatMessage->getAuthor()->getUsername(),
                        'imageFileName' => $chatMessage->getImageFileName(),
                    ]),
                );

                $this->hub->publish($update);
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }

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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($chatMessage->getText() === null && $chatMessage->getImageFileName() === null) {
                    return $this->redirectToRoute('home');
                }

                $this->entityManager->flush();
            }

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
            $user = $this->getUser();

            if ($user->isAdmin() || $user->isModerator() || $chatMessage->getAuthor() === $user) {
                $this->chatMessageRepository->remove($chatMessage, true);
            }
        }

        return $this->redirectToRoute('home');
    }
}
