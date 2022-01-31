<?php

namespace App\Service;

use Michelf\MarkdownInterface;
use Psr\Cache\CacheItemPoolInterface;

class MarkdownHelper
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private MarkdownInterface $markdown
    ) {
    }

    public function parse(string $source): string
    {
        // return $this->markdown->transform($source);
        $item = $this->cache->getItem('markdown_'.md5($source));

        if (!$item->isHit()) {
            $item->set($this->markdown->transform($source));
            $this->cache->save($item);
        }

        return $item->get();
    }

    public function clearCache(): bool
    {
        return $this->cache->clear();
    }
}
