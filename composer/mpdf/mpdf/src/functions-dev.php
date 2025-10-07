<?php

namespace OCA\Libresign\Vendor;

if (!\function_exists('OCA\\Libresign\\Vendor\\dd')) {
    /** @internal */
    function dd(...$args)
    {
        if (\function_exists('OCA\\Libresign\\Vendor\\dump')) {
            dump(...$args);
        } else {
            \var_dump(...$args);
        }
        die;
    }
}
