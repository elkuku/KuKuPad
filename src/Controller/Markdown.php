<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/markdown')]
class Markdown extends AbstractController
{
    /**
     * Converts a markdown string to HTML.
     *
     * @IsGranted("ROLE_EDITOR")
     */
    #[Route(path: '/preview', name: 'markdown_preview')]
    public function preview(
        Request $request,
        MarkdownHelper $markdownHelper
    ): JsonResponse {
        $text = $request->request->get('text');
        $data = $text ? $markdownHelper->parse($text) : ':(';

        return $this->json(
            [
                'data' => $data,
            ]
        );
    }

    /**
     * @IsGranted("ROLE_EDITOR")
     */
    #[Route(path: '/clearcache', name: 'markdown_clear_cache')]
    public function clearCache(
        MarkdownHelper $markdownHelper
    ): RedirectResponse {
        if (!$markdownHelper->clearCache()) {
            throw new \UnexpectedValueException('Can not clear the cache!');
        }
        $this->addFlash('success', 'Cache has been cleared.');

        return $this->redirectToRoute('default');
    }
}
