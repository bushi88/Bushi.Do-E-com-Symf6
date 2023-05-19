<?php

namespace App\Services;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartServices
{
    private $requestStack;
    private $em;
    private $tva = 0.2;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    // Ajoute un produit au panier
    public function addToCart($id)
    {
        // On récupère le contenu actuel du panier
        $cart = $this->getCart();

        // Le produit est déjà dans le panier
        if (isset($cart[$id])) {
            $cart[$id]++;
        }
        // Le produit n'est pas encore dans le panier
        else {
            $cart[$id] = 1;
        }
        // mise à jour du panier
        $this->updateCart($cart);
    }

    // Supprime un à un les exemplaires d'un produit du panier
    public function deleteFromCart($id)
    {
        // On récupère le contenu actuel du panier
        $cart = $this->getCart();

        // Le produit existe dans le panier
        if (isset($cart[$id])) {
            // La quantité du produit est supérieure à 1
            if ($cart[$id] > 1) {
                $cart[$id]--;
            }
            // La quantité du produit est égale à 1, on retire le produit
            else {
                unset($cart[$id]);
            }
            // mise à jour du panier
            $this->updateCart($cart);
        }
    }

    // Supprime d'un seul coup tous les exemplaires d'un produit du panier
    public function deleteAllToCart($id)
    {
        // On récupère le contenu actuel du panier
        $cart = $this->getCart();

        // Le produit est déjà dans le panier
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->updateCart($cart);
        }
    }

    // vide le panier en le réinitialisant
    public function deleteCart()
    {
        $this->updateCart([]);
    }

    // Met à jour le contenu du panier avec un tableau de produits et leurs quantités
    public function updateCart($cart)
    {
        $this->getSession()->set('cart', $cart);

        // pour récupérer toutes les données du panier dans la session (section 20 - cours 21)
        $this->getSession()->set('cartData', $this->getFullCart());
    }

    // Récupère le contenu actuel du panier
    public function getCart()
    {
        // on retourne le panier contenu dans la session
        // si le panier est vide, on retourne un tableau vide
        return $this->requestStack->getSession()->get('cart', []);
    }

    // Récupère le contenu complet du panier avec les informations détaillées sur chaque produit.
    public function getFullCart()
    {
        // On récupère le contenu actuel du panier
        $cart = $this->getCart();

        $fullCart = [];

        $quantity_cart = 0;
        $subTotal = 0;

        foreach ($cart as $id => $quantity) {
            $product = $this->em->getRepository(Product::class)->find($id);
            // si le produit a été récupéré avec succès
            if ($product) {
                $fullCart['products'][] = [
                    "quantity" => $quantity,
                    "product" => $product
                ];
                $quantity_cart += $quantity;
                $subTotal += $quantity * $product->getPrice() / 100;
            }
            // L'ID du produit est incorrect, donc il est supprimé du panier
            else {
                $this->deleteFromCart($id);
            }
        }

        $fullCart['data'] = [
            "quantity_cart" => $quantity_cart,
            "subTotalHT" => $subTotal,
            "tax" => round($subTotal * $this->tva, 2),
            "subTotalTTC" => round(($subTotal + ($subTotal * $this->tva)), 2),
        ];

        return $fullCart;
    }
}