<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\View;

use OCA\Libresign\Vendor\Pagerfanta\CursorPagerInterface;
use OCA\Libresign\Vendor\Pagerfanta\PagerfantaInterface;
use OCA\Libresign\Vendor\Pagerfanta\PagerInterface;
use OCA\Libresign\Vendor\Pagerfanta\Position\PagePosition;
use OCA\Libresign\Vendor\Pagerfanta\Position\Position;
use OCA\Libresign\Vendor\Pagerfanta\RouteGenerator\PageRouteGeneratorWrapper;
use OCA\Libresign\Vendor\Pagerfanta\RouteGenerator\PositionRouteGeneratorInterface;
use OCA\Libresign\Vendor\Pagerfanta\RouteGenerator\RouteGeneratorInterface;
use OCA\Libresign\Vendor\Pagerfanta\View\Template\SequentialTemplateInterface;
/**
 * View which renders previous and next links for any pager, independent of its pagination strategy.
 *
 * The previous link is omitted for cursor pagers which do not support backward navigation.
 * @internal
 */
final class SequentialView implements PagerViewInterface
{
    public function __construct(private readonly SequentialTemplateInterface $template, private readonly string $name = 'sequential')
    {
    }
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * @param PagerfantaInterface<mixed>|PagerInterface<mixed, Position> $pager
     */
    public function supports(PagerfantaInterface|PagerInterface $pager) : bool
    {
        return \true;
    }
    /**
     * @param PagerfantaInterface<mixed>|PagerInterface<mixed, Position>                    $pager
     * @param PositionRouteGeneratorInterface|RouteGeneratorInterface|callable(int): string $routeGenerator
     * @param array<string, mixed>                                                          $options
     */
    public function render(PagerfantaInterface|PagerInterface $pager, callable $routeGenerator, array $options = []) : string
    {
        if (!$routeGenerator instanceof PositionRouteGeneratorInterface) {
            trigger_deprecation('pagerfanta/core', '4.10', 'Passing a page number based route generator to "%s::render()" is deprecated, pass an instance of "%s" instead.', self::class, PositionRouteGeneratorInterface::class);
        }
        // Render from a copy of the template so the options and route generator of this render are not reused by the next one
        $template = clone $this->template;
        $template->setPositionRouteGenerator(PageRouteGeneratorWrapper::wrap($routeGenerator));
        $template->setOptions($options);
        return \str_replace('%pages%', $this->previous($template, $pager) . $this->next($template, $pager), $template->container());
    }
    /**
     * @param PagerfantaInterface<mixed>|PagerInterface<mixed, Position> $pager
     */
    private function previous(SequentialTemplateInterface $template, PagerfantaInterface|PagerInterface $pager) : string
    {
        if ($pager instanceof CursorPagerInterface && !$pager->supportsBackwardNavigation()) {
            return '';
        }
        if (!$pager->hasPreviousPage()) {
            return $template->previousDisabled();
        }
        // Implementations of PagerfantaInterface are not required to implement the position API until 5.0
        $position = $pager instanceof PagerInterface ? $pager->getPreviousPosition() : new PagePosition($pager->getPreviousPage());
        return $template->previousEnabledForPosition($position);
    }
    /**
     * @param PagerfantaInterface<mixed>|PagerInterface<mixed, Position> $pager
     */
    private function next(SequentialTemplateInterface $template, PagerfantaInterface|PagerInterface $pager) : string
    {
        if (!$pager->hasNextPage()) {
            return $template->nextDisabled();
        }
        $position = $pager instanceof PagerInterface ? $pager->getNextPosition() : new PagePosition($pager->getNextPage());
        return $template->nextEnabledForPosition($position);
    }
}
