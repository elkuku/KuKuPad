<?php

namespace App\Service;

use App\Repository\AgentRepository;
use App\Repository\PageRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MarkdownParser extends \Knp\Bundle\MarkdownBundle\Parser\MarkdownParser
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct();
        $this->urlGenerator = $urlGenerator;
        $this->pageRepository = $pageRepository;
    }

    public function transform($text)
    {
        $text = parent::transform($text);

        $text = $this->replaceAgentName($text);

        return $text;
    }

    private function replaceAgentName($text): string
    {
        $text = preg_replace_callback(
            '/\[\[([a-zA-Z0-9]+)\]\]/',
            function ($agentName) {
                $agent = $this->pageRepository->findOneBySlug($agentName[1]);

                if (!$agent) {
                    return '<code>'.$agentName[1].'</code>';
                }

                $url = $this->urlGenerator->generate('page_show2', ['slug' => $agent->getSlug()]);

                $linkText = $agentName[1];

                // $linkText = sprintf(
                //     '<img src="/build/images/logos/%s.svg" style="height: 32px" alt="logo"> %s',
                //     $agent->getFaction()->getName(),
                //     $agentName[0]
                // );

                return sprintf('<a href="%s">%s</a>', $url, $linkText);
            },
            $text
        );

        return $text;
    }
}
