<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Categories;
use App\Controller\Admin\OrderCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[Route('/admin')]
class DashboardController extends AbstractDashboardController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        // return $this->render('admin/admin.html.twig');

        // ouverture du dashboard sur la table des commandes
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Buhi.Do E-commerce');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour à l\'Accueil', 'fas fa-home', 'app_home');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Liste des Produits', 'fas fa-shopping-cart', Product::class);
        yield MenuItem::linkToCrud('Liste des Catégories', 'fas fa-list', Categories::class);
        yield MenuItem::linkToCrud('Liste des transporteurs', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Liste des Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Liste des commandes', 'fas fa-shopping-bag', Order::class);
        yield MenuItem::linkToCrud('Liste des paniers', 'fas fa-shopping-bag', Cart::class);
    }
}