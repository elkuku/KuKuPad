<?php

namespace App\Service;

use App\Repository\AgentRepository;
use App\Repository\PageRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MarkdownParser extends \Knp\Bundle\MarkdownBundle\Parser\MarkdownParser
{
    public function __construct(
        private PageRepository $pageRepository,
        private UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct();
    }

    public function transform($text)
    {
        $text = parent::transform($text);

        $text = str_replace('<a href', '<a class="external" href', $text);

        return $this->replaceLocalLink($text);
    }

    private function replaceLocalLink($text): string
    {
        return preg_replace_callback(
            '/\[\[([a-zA-Z0-9\s\-]+)\]\]/',
            function ($link) {
                $page = $this->pageRepository->findOneBySlug(
                    Slugger::slugify($link[1])
                );

                if (!$page) {
                    $url = $this->urlGenerator->generate(
                        'page_new',
                        ['title' => $link[1]]
                    );

                    $linkText = $link[1];
                    $cssClass = 'text-danger';
                } else {
                    $url = $this->urlGenerator->generate(
                        'wiki',
                        ['slug' => $page->getSlug()]
                    );

                    $linkText = $link[1];
                    $cssClass = '';
                }

                return sprintf(
                    '<a class="%s" href="%s">%s</a>',
                    $cssClass,
                    $url,
                    $linkText
                );
            },
            $text
        );
    }
}
