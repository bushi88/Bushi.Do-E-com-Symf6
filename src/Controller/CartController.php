<?php

namespace App\Controller;

use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    private $cartServices;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    #[Route('/', name: 'show')]
    public function show(): Response
    {
        $cart = $this->cartServices->getFullCart();

        if (!$cart) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'add')]
    public function addProductToCart($id)
    {
        $this->cartServices->addToCart($id);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function removeProductFromCart($id)
    {
        $this->cartServices->deleteFromCart($id);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteProductFromCart($id)
    {
        $this->cartServices->deleteAllToCart($id);

        return $this->redirectToRoute('app_cart_show');
    }
}