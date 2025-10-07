<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Twig\Extension;

use OCA\Libresign\Vendor\Twig\Extension\AbstractExtension;
use OCA\Libresign\Vendor\Twig\TwigFunction;
/** @internal */
final class PagerfantaExtension extends AbstractExtension
{
    /**
     * @return list<TwigFunction>
     */
    public function getFunctions() : array
    {
        return [new TwigFunction('pagerfanta', [PagerfantaRuntime::class, 'renderPagerfanta'], ['is_safe' => ['html']]), new TwigFunction('pagerfanta_page_url', [PagerfantaRuntime::class, 'getPageUrl'])];
    }
}
