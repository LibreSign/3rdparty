<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\View;

use OCA\Libresign\Vendor\Pagerfanta\View\Template\TemplateInterface;
use OCA\Libresign\Vendor\Pagerfanta\View\Template\TwitterBootstrap4Template;
/** @internal */
class TwitterBootstrap4View extends TwitterBootstrapView
{
    protected function createDefaultTemplate() : TemplateInterface
    {
        return new TwitterBootstrap4Template();
    }
    public function getName() : string
    {
        return 'twitter_bootstrap4';
    }
}
