<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use App\Services\CartServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeSuccessPaymentController extends AbstractController
{
    #[Route('/stripe-payment-success/{StripeCheckoutSessionId}', name: 'stripe_payment_success')]
    public function index(?Order $order, CartServices $cartServices, EntityManagerInterface $manager): Response
    {
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if (!$order->getIsPaid()) {
            // commande payée
            $order->setIsPaid(true);
            $manager->flush($order);
            // on vide le panier
            $cartServices->deleteCart();
            // email au client pour confirmer le paiement
            
        }

        return $this->render('stripe/stripe_success_payment/index.html.twig', [
            'controller_name' => 'StripeSuccessPaymentController',
            'order' => $order,
        ]);
    }
}