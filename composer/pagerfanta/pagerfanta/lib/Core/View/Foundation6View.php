<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\View;

use OCA\Libresign\Vendor\Pagerfanta\View\Template\Foundation6Template;
use OCA\Libresign\Vendor\Pagerfanta\View\Template\TemplateInterface;
/** @internal */
class Foundation6View extends TemplateView
{
    protected function createDefaultTemplate() : TemplateInterface
    {
        return new Foundation6Template();
    }
    protected function getDefaultProximity() : int
    {
        return 3;
    }
    public function getName() : string
    {
        return 'foundation6';
    }
}
