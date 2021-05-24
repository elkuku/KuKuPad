<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/wiki')]
class WikiController extends AbstractController
{
    /**
     * @IsGranted("ROLE_READER")
     */
    #[Route(path: '/{slug}', name: 'wiki', defaults: ['slug' => 'default'])]
    public function wiki(
        PageRepository $pageRepository,
        string $slug
    ): Response {
        $page = $pageRepository->findOneBy(['slug' => $slug]);
        if (!$page) {
            if ('default' === $slug) {
                throw $this->createNotFoundException('No default page found');
            }

            throw $this->createNotFoundException('The page does not exist');
        }

        return $this->render(
            'page/show.html.twig',
            [
                'page'          => $page,
                'default_title' => $_ENV['APP_WIKI_NAME'],
            ]
        );
    }
}
