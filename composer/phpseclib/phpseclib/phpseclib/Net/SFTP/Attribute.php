<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\Net\SFTP;

/**
 * http://tools.ietf.org/html/draft-ietf-secsh-filexfer-13#section-7.1
 * the order, in this case, matters quite a lot - see \phpseclib4\Net\SFTP::_parseAttributes() to understand why
 *
 * @internal
 */
abstract class Attribute
{
    public const SIZE = 0x1;
    public const UIDGID = 0x2;
    // defined in SFTPv3, removed in SFTPv4+
    public const OWNERGROUP = 0x80;
    // defined in SFTPv4+
    public const PERMISSIONS = 0x4;
    public const ACCESSTIME = 0x8;
    public const CREATETIME = 0x10;
    // SFTPv4+
    public const MODIFYTIME = 0x20;
    public const ACL = 0x40;
    public const SUBSECOND_TIMES = 0x100;
    public const BITS = 0x200;
    // SFTPv5+
    public const ALLOCATION_SIZE = 0x400;
    // SFTPv6+
    public const TEXT_HINT = 0x800;
    public const MIME_TYPE = 0x1000;
    public const LINK_COUNT = 0x2000;
    public const UNTRANSLATED_NAME = 0x4000;
    public const CTIME = 0x8000;
    // 0x80000000 will yield a floating point on 32-bit systems and converting floating points to integers
    // yields inconsistent behavior depending on how php is compiled.  so we left shift -1 (which, in
    // two's compliment, consists of all 1 bits) by 31.  on 64-bit systems this'll yield 0xFFFFFFFF80000000.
    // that's not a problem, however, and 'anded' and a 32-bit number, as all the leading 1 bits are ignored.
    public const EXTENDED = \PHP_INT_SIZE == 4 ? -1 << 31 : 0x80000000;
    /**
     */
    public static function getConstants() : array
    {
        $reflectionClass = new \ReflectionClass(static::class);
        return $reflectionClass->getConstants();
    }
}
