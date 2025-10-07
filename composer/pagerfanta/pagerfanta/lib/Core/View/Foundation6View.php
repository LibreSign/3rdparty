<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\View;

use OCA\Libresign\3rdparty\Pagerfanta\View\Template\Foundation6Template;
use OCA\Libresign\3rdparty\Pagerfanta\View\Template\TemplateInterface;
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
