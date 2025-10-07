<?php

namespace OCA\Libresign\Vendor\Wobeto\EmailBlur;

use http\Exception\InvalidArgumentException;
/** @internal */
class Blur
{
    protected string $email;
    protected int $total_mask = 3;
    protected bool $show_domain = \false;
    public function __construct(string $email)
    {
        if (!\filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('E-mail is invalid');
        }
        $this->email = $email;
    }
    public function setTotalMask(int $total)
    {
        $this->total_mask = $total;
        return $this;
    }
    public function showDomain()
    {
        $this->show_domain = \true;
        return $this;
    }
    public function make()
    {
        list($prefix, $domain) = \explode('@', $this->email);
        $prefix = $this->maskEmailPrefix($prefix);
        if ($this->show_domain === \false) {
            $domain = $this->maskEmailDomain($domain);
        }
        return $prefix . '@' . $domain;
    }
    private function maskEmailPrefix(string $prefix) : string
    {
        $length = \strlen($prefix) > 3 ? 3 : 1;
        return \substr($prefix, 0, $length) . \str_repeat('*', $this->total_mask);
    }
    private function maskEmailDomain(string $domain) : string
    {
        $domain = \explode('.', $domain);
        \array_shift($domain);
        return \str_repeat('*', $this->total_mask) . '.' . \implode('.', $domain);
    }
}
