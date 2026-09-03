<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\Net\SFTP;

/**
 * SFTPv5+ changed the flags up: https://datatracker.ietf.org/doc/html/draft-ietf-secsh-filexfer-13#section-8.1.1.3
 *
 * @internal
 */
abstract class OpenFlag5
{
    // when SSH_FXF_ACCESS_DISPOSITION is a 3 bit field that controls how the file is opened
    public const CREATE_NEW = 0x0;
    public const CREATE_TRUNCATE = 0x1;
    public const OPEN_EXISTING = 0x2;
    public const OPEN_OR_CREATE = 0x3;
    public const TRUNCATE_EXISTING = 0x4;
    // the rest of the flags are not supported
    public const APPEND_DATA = 0x8;
    // "the offset field of SS_FXP_WRITE requests is ignored"
    public const APPEND_DATA_ATOMIC = 0x10;
    public const TEXT_MODE = 0x20;
    public const BLOCK_READ = 0x40;
    public const BLOCK_WRITE = 0x80;
    public const BLOCK_DELETE = 0x100;
    public const BLOCK_ADVISORY = 0x200;
    public const NOFOLLOW = 0x400;
    public const DELETE_ON_CLOSE = 0x800;
    public const ACCESS_AUDIT_ALARM_INFO = 0x1000;
    public const ACCESS_BACKUP = 0x2000;
    public const BACKUP_STREAM = 0x4000;
    public const OVERRIDE_OWNER = 0x8000;
}
