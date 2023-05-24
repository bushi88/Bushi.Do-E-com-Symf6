<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/session', name: 'session_')]
class SessionController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Récupère l'objet SessionInterface à partir de la RequestStack.
     *
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    #[Route('/', name: 'test')]
    public function show(): JsonResponse
    {
        // Récupère la session
        $session = $this->getSession();

        // Définit une variable "test" dans la session avec une valeur de tableau
        $session->set('test', ["name" => "session"]);

        // Récupère la valeur de "test" depuis la session
        $test = $session->get("test");

        // Retourne la réponse JSON avec les données du test
        return $this->json($test);
    }
}