<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\View;

use OCA\Libresign\3rdparty\Pagerfanta\View\Template\SemanticUiTemplate;
use OCA\Libresign\3rdparty\Pagerfanta\View\Template\TemplateInterface;
/** @internal */
class SemanticUiView extends TemplateView
{
    protected function createDefaultTemplate() : TemplateInterface
    {
        return new SemanticUiTemplate();
    }
    protected function getDefaultProximity() : int
    {
        return 3;
    }
    public function getName() : string
    {
        return 'semantic_ui';
    }
}
