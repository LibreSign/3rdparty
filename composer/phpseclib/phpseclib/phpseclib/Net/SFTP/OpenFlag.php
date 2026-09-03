<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\Net\SFTP;

/**
 * http://tools.ietf.org/html/draft-ietf-secsh-filexfer-04#section-6.3
 * the flag definitions change somewhat in SFTPv5+.  if SFTPv5+ support is added to this library, maybe name
 * the array for that $this->open5_flags and similarly alter the constant names.
 *
 * @internal
 */
abstract class OpenFlag
{
    public const READ = 0x1;
    public const WRITE = 0x2;
    public const APPEND = 0x4;
    public const CREATE = 0x8;
    public const TRUNCATE = 0x10;
    public const EXCL = 0x20;
    public const TEXT = 0x40;
    // defined in SFTPv4
}
