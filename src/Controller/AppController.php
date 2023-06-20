<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('index.html.twig', [
            'users' => $users,
        ]);
    }
}