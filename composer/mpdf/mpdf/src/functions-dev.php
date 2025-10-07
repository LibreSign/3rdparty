<?php

namespace OCA\Libresign\3rdparty;

if (!\function_exists('OCA\\Libresign\\3rdparty\\dd')) {
    /** @internal */
    function dd(...$args)
    {
        if (\function_exists('OCA\\Libresign\\3rdparty\\dump')) {
            dump(...$args);
        } else {
            \var_dump(...$args);
        }
        die;
    }
}
