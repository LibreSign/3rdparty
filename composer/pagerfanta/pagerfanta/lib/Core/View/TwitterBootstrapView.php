<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\View;

use OCA\Libresign\3rdparty\Pagerfanta\View\Template\TemplateInterface;
use OCA\Libresign\3rdparty\Pagerfanta\View\Template\TwitterBootstrapTemplate;
/** @internal */
class TwitterBootstrapView extends TemplateView
{
    protected function createDefaultTemplate() : TemplateInterface
    {
        return new TwitterBootstrapTemplate();
    }
    protected function getDefaultProximity() : int
    {
        return 3;
    }
    public function getName() : string
    {
        return 'twitter_bootstrap';
    }
}
