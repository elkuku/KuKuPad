<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Service\Slugger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 * @IsGranted("ROLE_ADMIN")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/", name="page_index", methods={"GET"})
     */
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render(
            'page/index.html.twig', [
                'pages' => $pageRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new/{title}", name="page_new", methods={"GET","POST"}, defaults={"title"="New Page"})
     */
    public function new(Request $request, string $title): Response
    {
        $page = new Page();
        if ($title) {
            $page->setTitle($title);
        }
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug(Slugger::slugify($page->getTitle()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('page_index');
        }

        return $this->render(
            'page/new.html.twig', [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="page_show", methods={"GET"})
     */
    public function show(Page $page): Response
    {
        return $this->render(
            'page/show.html.twig', [
            'page'          => $page,
            'default_title' => $_ENV['APP_WIKI_NAME'],
        ]
        );
    }

    /**
     * @Route("/show/{slug}", name="page_show2", methods={"GET"})
     */
    public function show2(string $slug, PageRepository $helpRepository): Response
    {
        $page = $helpRepository->findOneBy(['slug' => $slug]);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'page/show.html.twig', [
            'page'          => $page,
            'default_title' => $_ENV['APP_WIKI_NAME'],
        ]
        );
    }

    /**
     * @Route("/{id}/edit", name="page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug(Slugger::slugify($page->getTitle()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('page_index');
        }

        return $this->render(
            'page/edit.html.twig', [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Page $page): Response
    {
        if ($this->isCsrfTokenValid(
            'delete'.$page->getId(), $request->request->get('_token')
        )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($page);
            $entityManager->flush();
        }

        return $this->redirectToRoute('page_index');
    }
}
