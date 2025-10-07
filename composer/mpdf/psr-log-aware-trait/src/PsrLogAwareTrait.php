<?php

namespace OCA\Libresign\Vendor\Mpdf\PsrLogAwareTrait;

use OCA\Libresign\Vendor\Psr\Log\LoggerInterface;
/** @internal */
trait PsrLogAwareTrait
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }
}
