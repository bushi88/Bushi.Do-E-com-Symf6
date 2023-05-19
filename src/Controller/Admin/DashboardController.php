<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Categories;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[Route('/admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Buhido E-commerce');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour à l\'Accueil', 'fas fa-home', 'app_home');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Liste des Produits', 'fas fa-shopping-cart', Product::class);
        yield MenuItem::linkToCrud('Liste des Catégories', 'fas fa-list', Categories::class);
        yield MenuItem::linkToCrud('Liste des transporteurs', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Liste des Utilisateurs', 'fas fa-user', User::class);
    }
}