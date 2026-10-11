<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use TailwindUi\Command\CopyLayoutsCommand;

class CopyLayoutsCommandTest extends TestCase
{
    public function testWriteFailureReturnsError(): void
    {
        $command = $this->getMockBuilder(CopyLayoutsCommand::class)
            ->onlyMethods(['applicationTemplatesPath'])->getMock();
        $command->expects($this->once())->method('applicationTemplatesPath')
            ->willReturn(TMP . 'tailwind-copy-' . uniqid() . DS);
        $io = $this->getMockBuilder(ConsoleIo::class)
            ->onlyMethods(['createFile', 'out'])->getMock();
        $io->expects($this->exactly(2))->method('createFile')->willReturn(false);
        $this->assertSame(CopyLayoutsCommand::CODE_ERROR, $command->execute(new Arguments([], [], []), $io));
    }
}
