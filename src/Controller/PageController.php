<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Service\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/page')]
#[IsGranted('ROLE_EDITOR')]
class PageController extends AbstractController
{
    #[Route(path: '/', name: 'page_index', methods: ['GET'])]
    public function index(
        PageRepository $pageRepository
    ): Response {
        return $this->render(
            'page/index.html.twig',
            [
                'pages' => $pageRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new/{title}', name: 'page_new', defaults: ['title' => 'New Page'], methods: [
        'GET',
        'POST',
    ])]
    public function new(
        string $title,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $page = new Page();
        if ($title) {
            $page->setTitle($title);
        }
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug(Slugger::slugify($page->getTitle()));
            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('wiki', ['slug' => $page->getSlug()]);
        }

        return $this->render(
            'page/new.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'page_show', methods: ['GET'])]
    public function show(
        Page $page
    ): Response {
        return $this->render(
            'page/show.html.twig',
            [
                'page'          => $page,
                'default_title' => $_ENV['APP_WIKI_NAME'],
            ]
        );
    }

    #[Route(path: '/show/{slug}', name: 'page_show2', methods: ['GET'])]
    public function show2(
        string $slug,
        PageRepository $helpRepository
    ): Response {
        $page = $helpRepository->findOneBy(['slug' => $slug]);
        if (!$page) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'page/show.html.twig',
            [
                'page'          => $page,
                'default_title' => $_ENV['APP_WIKI_NAME'],
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'page_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Page $page,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug(Slugger::slugify($page->getTitle()));

            $entityManager->flush();

            return $this->redirectToRoute('wiki', ['slug' => $page->getSlug()]);
        }

        return $this->render(
            'page/edit.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'page_delete', methods: ['DELETE'])]
    public function delete(
        Request $request,
        Page $page,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete'.$page->getId(),
            $request->request->get('_token')
        )
        ) {
            $entityManager->remove($page);
            $entityManager->flush();
        }

        return $this->redirectToRoute('page_index');
    }
}
