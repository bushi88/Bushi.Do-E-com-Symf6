<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cgu', name: 'app_cgu_')]
class CGUController extends AbstractController
{
    #[Route('/conditions-generales', name: 'conditionsG')]
    public function index(): Response
    {
        return $this->render('cgu/index.html.twig', [
            'controller_name' => 'CGUController',
        ]);
    }
}