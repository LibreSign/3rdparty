<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\View\Template;

/** @internal */
class TwitterBootstrap5Template extends TwitterBootstrap4Template
{
    /**
     * @return array<string, string>
     */
    protected function getDefaultOptions() : array
    {
        return [...parent::getDefaultOptions(), ...['active_suffix' => '<span class="visually-hidden">(current)</span>']];
    }
}
