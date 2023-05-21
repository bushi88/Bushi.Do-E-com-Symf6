<?php

namespace App\Controller\Cart;

use App\Form\CheckoutType;
use App\Services\CartServices;
use App\Services\OrderServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/checkout', name: 'app_checkout_')]
class CheckoutController extends AbstractController
{
    private $cartServices;
    private $requestStack;

    public function __construct(CartServices $cartServices, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->cartServices = $cartServices;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    #[Route('/', name: 'show')]
    public function show(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Obtenir le panier complet
        $cart = $this->cartServices->getFullCart();

        // Vérifier si le panier est vide
        if (!isset($cart['products'])) {
            return $this->redirectToRoute('app_home');
        }

        // Vérifier si l'utilisateur a des adresses enregistrées
        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Merci de renseigner une adresse de livraison avant de continuer !');
            return $this->redirectToRoute('app_address_new');
        }

        if ($this->requestStack->getSession()->get('checkout_data')) {
            return $this->redirectToRoute('app_checkout_confirm');
        }

        // Créer le formulaire de validation de commande
        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);

        // Afficher la vue de récapitulatif de commande avec les données nécessaires
        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'checkout' => $form->createView()
        ]);
    }

    #[Route('/confirm', name: 'confirm')]
    public function confirm(Request $request, OrderServices $orderservices): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Obtenir le panier complet
        $cart = $this->cartServices->getFullCart();

        // Vérifier si le panier est vide
        if (!isset($cart['products'])) {
            return $this->redirectToRoute("app_home");
        }

        // Vérifier si l'utilisateur a des adresses enregistrées
        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Merci de renseigner une adresse de livraison avant de continuer !');
            return $this->redirectToRoute("address_new");
        }

        // Créer le formulaire de validation de commande
        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);

        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide, ou si des données de validation de commande existent en session
        if ($form->isSubmitted() && $form->isValid() || $this->requestStack->getSession()->get('checkout_data')) {

            // pour ne pas obliger l'utilisateur a valider le formulaire checkout plusieurs fois
            if ($this->requestStack->getSession()->get('checkout_data')) {
                // Utiliser les données de validation de commande en session
                $data = $this->requestStack->getSession()->get('checkout_data');
            } else {
                // sinon utiliser les données du formulaire soumis et les enregistrer dans la session
                $data = $form->getData();
                $this->getSession()->set('checkout_data', $data);
            }

            // Récupérer les informations de l'adresse, du transporteur et des informations supplémentaires
            $address = $data['address'];
            $carrier = $data['carrier'];
            $informations = $data['informations'];

            // Sauvegarde du panier
            $cart['checkout'] = $data;
            $reference = $orderservices->saveCart($cart, $user);

            // Afficher la vue de confirmation de commande avec les données nécessaires
            return $this->render('checkout/confirm.html.twig', [
                'cart' => $cart,
                'address' => $address,
                'carrier' => $carrier,
                'informations' => $informations,
                'reference' => $reference,
                'checkout' => $form->createView(),
            ]);
        }

        // Rediriger vers la page de validation de commande
        return $this->redirectToRoute("app_checkout_show");
    }

    #[Route('/edit', name: 'edit')]
    public function checkoutEdit(): Response
    {
        $this->getSession()->set('checkout_data', []);
        return $this->redirectToRoute('app_checkout_show');
    }
}