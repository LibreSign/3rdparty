<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Symfony\Component\Process\Messenger;

use OCA\Libresign\Vendor\Symfony\Component\Process\Process;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 * @internal
 */
final class RunProcessContext
{
    public readonly ?int $exitCode;
    public readonly ?string $output;
    public readonly ?string $errorOutput;
    public function __construct(public readonly RunProcessMessage $message, Process $process)
    {
        $this->exitCode = $process->getExitCode();
        $this->output = $process->isOutputDisabled() ? null : $process->getOutput();
        $this->errorOutput = $process->isOutputDisabled() ? null : $process->getErrorOutput();
    }
}
