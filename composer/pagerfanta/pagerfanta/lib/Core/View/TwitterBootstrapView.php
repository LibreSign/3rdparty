<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\View;

use OCA\Libresign\Vendor\Pagerfanta\View\Template\TemplateInterface;
use OCA\Libresign\Vendor\Pagerfanta\View\Template\TwitterBootstrapTemplate;
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
