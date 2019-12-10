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
    /**
     * @var MarkdownHelper
     */
    private $markdownHelper;

    public function __construct(MarkdownHelper $markdownHelper)
    {
        $this->markdownHelper = $markdownHelper;
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

    public function getFunctions()
    {
        return [
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
