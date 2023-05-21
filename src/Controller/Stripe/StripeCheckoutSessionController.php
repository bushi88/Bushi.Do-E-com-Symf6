<?php

namespace App\Controller\Stripe;

use Stripe\Stripe;
use App\Entity\Cart;
use Stripe\Checkout\Session;
// use App\Services\CartServices;
use App\Services\OrderServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeCheckoutSessionController extends AbstractController
{
    #[Route('/create-checkout-session/{reference}', name: 'create_checkout_session')]
    public function index(?Cart $cart, OrderServices $orderServices, EntityManagerInterface $manager): JsonResponse
    {
        // commenter => cf section 21 cours 34
        // $cart = $cartServices->getFullCart();

        $user = $this->getUser();

        if (!$cart) {
            return $this->redirectToRoute('app_home');
        }

        $order = $orderServices->createOrder($cart);

        // paiement via stripe
        $stripeSK = $_ENV['STRIPE_SK'];
        Stripe::setApiKey($stripeSK);

        $checkout_session = Session::create([
            'customer_email' => $user->getEmail(),
            'line_items' => $orderServices->getLineItems($cart),
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-cancel/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeCheckoutSessionId($checkout_session->id);
        $manager->flush($order);

        return $this->json(['id' => $checkout_session->id]);
    }
}