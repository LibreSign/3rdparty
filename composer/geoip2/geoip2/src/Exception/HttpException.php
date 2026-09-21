<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\GeoIp2\Exception;

/**
 *  This class represents an HTTP transport error.
 * @internal
 */
class HttpException extends GeoIp2Exception
{
    /**
     * The URI queried.
     */
    public string $uri;
    public function __construct(string $message, int $httpStatus, string $uri, ?\Exception $previous = null)
    {
        $this->uri = $uri;
        parent::__construct($message, $httpStatus, $previous);
    }
}
