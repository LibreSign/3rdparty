<?php

namespace OCA\Libresign\3rdparty\Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 * @internal
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     */
    protected ?LoggerInterface $logger = null;
    /**
     * Sets a logger.
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }
}
