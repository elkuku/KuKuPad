<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'default')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_READER')) {
            return $this->redirectToRoute('wiki');
        }

        return $this->render('default/index.html.twig', []);
    }
}
