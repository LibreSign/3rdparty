<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\View;

use OCA\Libresign\3rdparty\Pagerfanta\View\Template\TemplateInterface;
use OCA\Libresign\3rdparty\Pagerfanta\View\Template\TwitterBootstrap5Template;
/** @internal */
class TwitterBootstrap5View extends TwitterBootstrapView
{
    protected function createDefaultTemplate() : TemplateInterface
    {
        return new TwitterBootstrap5Template();
    }
    public function getName() : string
    {
        return 'twitter_bootstrap5';
    }
}
