<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 19.03.17
 * Time: 12:40
 */

namespace App\Twig;

use App\Service\MarkdownHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class AppExtension
 */
class AppExtension extends AbstractExtension
{
    public function __construct(private MarkdownHelper $markdownHelper)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'md2html', [
                $this,
                'markdownToHtml',
            ], ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Transforms the given Markdown content into HTML content.
     */
    public function markdownToHtml(string $content): string
    {
        return $this->markdownHelper->parse($content);
    }
}
