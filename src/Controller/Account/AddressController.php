<?php

namespace App\Controller\Account;

use App\Entity\Address;
use App\Form\AddressType;
use App\Services\CartServices;
use App\Repository\AddressRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/address', name: 'app_address_')]
class AddressController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(AddressRepository $addressRepository): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $addressRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, AddressRepository $addressRepository, CartServices $cartServices): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on ajoute le user
            $user = $this->getUser();
            $address->setUser($user);

            $addressRepository->save($address, true);

            if ($cartServices->getFullCart()) {
                return $this->redirectToRoute('app_checkout_show');
            }

            $this->addFlash('address_message', 'Your address has been saved');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'show', methods: ['GET'])]
    // public function show(Address $address): Response
    // {
    //     return $this->render('address/show.html.twig', [
    //         'address' => $address,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Address $address, AddressRepository $addressRepository): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressRepository->save($address, true);

            if ($this->requestStack->getSession()->get('checkout_data')) {
                $data = $this->requestStack->getSession()->get('checkout_data');
                $data['address'] = $address;
                $this->requestStack->getSession()->set('checkout_data', $data);

                return $this->redirectToRoute('app_checkout_confirm');
            }

            $this->addFlash('address_message', 'Your address has been edited');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Address $address, AddressRepository $addressRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->request->get('_token'))) {
            $addressRepository->remove($address, true);
        }

        $this->addFlash('address_message', 'Your address has been deleted');

        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }
}