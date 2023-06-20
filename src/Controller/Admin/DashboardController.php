<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

         $adminUrl = $adminUrlGenerator->setController(UserCrudController::class)->generateUrl();

         return $this->redirect($adminUrl);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Php Symfony Chat');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::subMenu('Users', 'fa fa-article')->setSubItems([
                MenuItem::linkToCrud('Users list', 'fas fa-list', User::class),

                MenuItem::linkToCrud('Add User', 'fas fa-plus', User::class)
                    ->setAction(Action::NEW),
            ]),
        ];
    }
}
