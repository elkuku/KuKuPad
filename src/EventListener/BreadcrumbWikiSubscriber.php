<?php

namespace App\EventListener;

use App\Repository\PageRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbWikiSubscriber implements EventSubscriberInterface
{
    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository, AdapterInterface $cache, Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        $this->cache = $cache;
        $this->breadcrumbs = $breadcrumbs;
        $this->urlGenerator = $urlGenerator;
        $this->pageRepository = $pageRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            'kernel.request' => 'onKernelRequest',
        );
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $slug = $event->getRequest()->attributes->get('slug');

        if (!$slug) {
            return;
        }

        $page = $this->pageRepository->findOneBySlug($slug);

        if (!$page) {
            return;
        }

        $url = $this->urlGenerator->generate('wiki', ['slug' => $slug]);

        $text = 'default' === $slug
            ? $_ENV['APP_WIKI_NAME']
            : $page->getTitle();

        try {
            $item = $this->cache->getItem('breadcrumbs');
        } catch (InvalidArgumentException $e) {
            return;
        }

        if ($item->isHit()) {
            $values = $item->get();
            if (array_key_exists($text, $values)) {
                unset($values[$text]);
            }
            $values[$text] = $url;
            foreach ($values as $itemText => $itemUrl) {
                $this->breadcrumbs->addItem($itemText, $itemUrl, [], false);
            }
            $item->set($values);
        } else {
            $item->set([$text => $url]);
            $this->breadcrumbs->addItem($text, $url, [], false);
        }

        // $item->set([]);

        $this->cache->save($item);
    }
}
