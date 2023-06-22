<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ChatMessage;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ChatMessageFormHandler
{
    public function __construct(
        private Security $security,
        private SluggerInterface $slugger,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function handleNewChatMessageForm(FormInterface $form): ChatMessage
    {
        $chatMessage = $form->getData();

        $user = $this->security->getUser();

        $chatMessage->setAuthor($user);

        $imageFile = $form->get('image')->getData();

        if ($chatMessage->getText() === null && $imageFile === null) {
            throw new BadRequestException('Message can not be empty');
        }

        if ($imageFile) {
            $newFileName = $this->generateNewFileName($imageFile);

            $imageFile->move(
                $this->parameterBag->get('kernel.project_dir') . '/public/uploads/chat_images',
                $newFileName,
            );

            $chatMessage->setImageFileName($newFileName);
        }

        return $chatMessage;
    }

    private function generateNewFileName($imageFile): string
    {
        $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFileName = $this->slugger->slug($originalFileName);

        return $safeFileName.'-'.uniqid().'.'.$imageFile->guessExtension();
    }
}
