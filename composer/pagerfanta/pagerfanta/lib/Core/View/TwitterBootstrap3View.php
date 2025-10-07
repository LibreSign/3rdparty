<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\View;

use OCA\Libresign\Vendor\Pagerfanta\View\Template\TemplateInterface;
use OCA\Libresign\Vendor\Pagerfanta\View\Template\TwitterBootstrap3Template;
/** @internal */
class TwitterBootstrap3View extends TwitterBootstrapView
{
    protected function createDefaultTemplate() : TemplateInterface
    {
        return new TwitterBootstrap3Template();
    }
    public function getName() : string
    {
        return 'twitter_bootstrap3';
    }
}
