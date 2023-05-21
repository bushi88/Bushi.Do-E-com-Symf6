<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\CartDetails;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderServices
{

    private $em;
    private $repoProduct;

    public function __construct(EntityManagerInterface $em, ProductRepository $repoProduct)
    {
        $this->em = $em;
        $this->repoProduct = $repoProduct;
    }

    public function createOrder($cart)
    {
        $order = new Order();
        $order->setReference($cart->getReference())
            ->setFullname($cart->getFullName())
            ->setCarrierName($cart->getCarrierName())
            ->setCarrierPrice($cart->getCarrierPrice() / 100)
            ->setDeliveryAddress($cart->getDeliveryAddress())
            ->setMoreInformation($cart->getMoreInformation())
            ->setCreatedAt($cart->getCreatedAt())
            ->setUser($cart->getUser())
            ->setQuantity($cart->getQuantity())
            ->setSubTotalHT($cart->getSubTotalHT() / 100)
            ->setTax($cart->getTax() / 100)
            ->setSubTotalTTC($cart->getSubTotalTTC() / 100);
        $this->em->persist($order);

        $products = $cart->getCartDetails()->getValues();
        foreach ($products as $cart_product) {
            $orderDetails = new OrderDetails();
            $orderDetails->setOrders($order)
                ->setProductName($cart_product->getProductName())
                ->setProductPrice($cart_product->getProductPrice())
                ->setQuantity($cart_product->getQuantity())
                ->setSubTotalHT($cart_product->getSubTotalHT())
                ->setTax($cart_product->getTax())
                ->setSubTotalTTC($cart_product->getSubTotalTTC());

            $this->em->persist($orderDetails);
        }

        $this->em->flush();

        return $order;
    }

    public function getLineItems($cart)
    {
        $cartDetails = $cart->getCartDetails();

        $line_items = [];

        foreach ($cartDetails as $details) {
            $product = $this->repoProduct->findOneByName($details->getProductName());

            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $product->getPrice(), // nb entier impératif
                    'product_data' => [
                        'name' => $product->getName(),
                        'images' => [$_ENV['YOUR_DOMAIN'] . '/uploads/products/' . $product->getImage()],
                    ],
                ],
                'quantity' => $details->getQuantity(),
            ];
        }

        // Ajout du transporteur
        $line_items[] = [
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $cart->getCarrierPrice(), // nb entier impératif
                'product_data' => [
                    'name' => 'Carrier (' . $cart->getCarrierName() . ')',
                    'images' => [$_ENV['YOUR_DOMAIN'] . '/uploads/products/'],
                ],
            ],
            'quantity' => 1,
        ];

        // ajout de la taxe
        $line_items[] = [
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $cart->getTax(), // nb entier impératif
                'product_data' => [
                    'name' => 'tva (20%)',
                    'images' => [$_ENV['YOUR_DOMAIN'] . '/uploads/products/'],
                ],
            ],
            'quantity' => 1,
        ];

        return $line_items;
    }

    public function saveCart($data, $user)
    {
        /*[
            'products' => [],//tous les produits du panier
            'data' => [],//sous-total, taxe, totalTTC
            'checkout' => [
                'address' => objet,
                'carrier' => objet,
                'informations' => prddkdkf
            ]
        ]*/

        //creation de l'objet cart
        $cart = new Cart();
        $reference = $this->generateUuid();
        $address = $data['checkout']['address'];
        $carrier = $data['checkout']['carrier'];
        $informations = $data['checkout']['informations'];

        $cart->setReference($reference)
            ->setCarrierName($carrier->getName())
            ->setCarrierPrice($carrier->getPrice() / 100)
            ->setFullname($address->getFullName())
            ->setDeliveryAddress($address)
            ->setMoreInformation($informations)
            ->setQuantity($data['data']['quantity_cart']) //voir dans CartServices.php
            ->setSubTotalHT($data['data']['subTotalHT'])
            ->setTax($data['data']['tax'])
            ->setSubTotalTTC($data['data']['subTotalTTC'] + $carrier->getPrice() / 100)
            ->setUser($user);
        $this->em->persist($cart);

        //creation de l'objet cartDetails
        $cart_details_array = [];

        foreach ($data['products'] as $products) {
            $cartDetails = new CartDetails();
            $subtotal = $products['quantity'] * $products['product']->getPrice() / 100;
            $cartDetails->setCarts($cart)
                ->setProductName($products['product']->getName())
                ->setProductPrice($products['product']->getPrice() / 100)
                ->setQuantity($products['quantity'])
                ->setSubtotalHt($subtotal)
                ->setTax(round($subtotal * 0.2), 2)
                ->setSubTotalTtc($subtotal * 1.2);

            $this->em->persist($cartDetails);
            $cart_details_array[] = $cartDetails;
        }
        $this->em->flush();

        return $reference;
    }

    // Fonction pour générer une référence unique
    public function generateUuid()
    {
        // Initialise le générateur de nombres aléatoires Mersenne Twister
        mt_srand((float)microtime() * 100000);

        //strtoupper : Renvoie une chaîne en majuscules
        //uniqid : Génère un identifiant unique
        $charid = strtoupper(md5(uniqid(rand(), true)));

        //Générer une chaîne d'un octet à partir d'un nombre
        $hyphen = chr(45);

        //substr : Retourne un segment de chaîne
        $uuid = ""
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);

        return $uuid;
    }
}