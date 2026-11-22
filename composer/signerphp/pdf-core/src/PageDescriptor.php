<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore;

/** @internal */
final readonly class PageDescriptor
{
    /**
     * @param  array<int, mixed>  $size
     */
    public function __construct(public int $id, public array $size)
    {
    }
}
