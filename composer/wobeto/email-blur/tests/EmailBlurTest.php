<?php

namespace OCA\Libresign\Vendor\Wobeto\EmailBlur\Tests;

use OCA\Libresign\Vendor\PHPUnit\Framework\TestCase;
use OCA\Libresign\Vendor\Wobeto\EmailBlur\Blur;
/** @internal */
class EmailBlurTest extends TestCase
{
    public function testDefaultBlur()
    {
        $blur = new Blur('example@test.com');
        $obscured = $blur->make();
        $this->assertEquals('exa***@***.com', $obscured);
    }
    public function testDefaultBlurShortEmail()
    {
        $blur = new Blur('jo@test.com');
        $obscured = $blur->make();
        $this->assertEquals('j***@***.com', $obscured);
    }
    public function testDefaultBlurComBr()
    {
        $blur = new Blur('example@test.com.br');
        $obscured = $blur->make();
        $this->assertEquals('exa***@***.com.br', $obscured);
    }
    public function testBlurWithMaskChanged()
    {
        $blur = new Blur('example@test.com');
        $obscured = $blur->setTotalMask(5)->make();
        $this->assertEquals('exa*****@*****.com', $obscured);
    }
    public function testBlurWithDomainVisible()
    {
        $blur = new Blur('example@test.com');
        $obscured = $blur->showDomain()->make();
        $this->assertEquals('exa***@test.com', $obscured);
    }
}
