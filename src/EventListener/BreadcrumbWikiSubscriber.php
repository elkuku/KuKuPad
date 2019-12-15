<?php

namespace App\EventListener;

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

    public function __construct(AdapterInterface $cache, Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        $this->cache = $cache;
        $this->breadcrumbs = $breadcrumbs;
        $this->urlGenerator = $urlGenerator;
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

        $url = $this->urlGenerator->generate('wiki', ['slug' => $slug]);

        try {
            $item = $this->cache->getItem('breadcrumbs');
        } catch (InvalidArgumentException $e) {
            return;
        }

        if ($item->isHit()) {
            $values = $item->get();
            if (array_key_exists($slug, $values)) {
                unset($values[$slug]);
            }
            $values[$slug] = $url;
            foreach ($values as $itemText => $itemUrl) {
                $this->breadcrumbs->addItem($itemText, $itemUrl);
            }
            $item->set($values);
        } else {
            $item->set([$slug => $url]);
            $this->breadcrumbs->addItem($slug, $url);
        }

        // $item->set([]);

        $this->cache->save($item);
    }
}
