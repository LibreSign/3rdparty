<?php

namespace OCA\Libresign\3rdparty\Mpdf\PsrLogAwareTrait;

use OCA\Libresign\3rdparty\Psr\Log\LoggerInterface;
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
