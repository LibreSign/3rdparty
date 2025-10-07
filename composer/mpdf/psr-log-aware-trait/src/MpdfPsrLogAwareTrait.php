<?php

namespace OCA\Libresign\3rdparty\Mpdf\PsrLogAwareTrait;

use OCA\Libresign\3rdparty\Psr\Log\LoggerInterface;
/** @internal */
trait MpdfPsrLogAwareTrait
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
        if (\property_exists($this, 'services') && \is_array($this->services)) {
            foreach ($this->services as $name) {
                if ($this->{$name} && $this->{$name} instanceof \OCA\Libresign\3rdparty\Psr\Log\LoggerAwareInterface) {
                    $this->{$name}->setLogger($logger);
                }
            }
        }
    }
}
