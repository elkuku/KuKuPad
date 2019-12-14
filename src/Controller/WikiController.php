<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wiki")
 */
class WikiController extends AbstractController
{
    /**
     * @Route("/{slug}", name="wiki", defaults={"slug"="default"})
     */
    public function index(PageRepository $pageRepository, string $slug)
    {
        $page = $pageRepository->findOneBy(['slug' => $slug]);

        if (!$page) {
            if ('default' === $slug) {
                throw $this->createNotFoundException('No default page found');
            }

            throw $this->createNotFoundException('The page does not exist');
        }

        return $this->render('page/show.html.twig', ['page' => $page,]);
    }
}
