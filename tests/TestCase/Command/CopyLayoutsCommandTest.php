<?php

declare(strict_types=1);

namespace TailwindUi\Test\TestCase\Command;

use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\StubConsoleOutput;
use Cake\TestSuite\TestCase;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
        $io->expects($this->exactly(3))->method('createFile')->willReturn(false);
        $this->assertSame(CopyLayoutsCommand::CODE_ERROR, $command->run([], $io));
    }

    public function testCopyUsesPluginOverridePathsAndPreservesExistingFiles(): void
    {
        $path = TMP . 'tailwind-copy-success-' . uniqid() . DS;
        mkdir($path . 'plugin/TailwindUi/layout', 0770, true);
        file_put_contents($path . 'plugin/TailwindUi/layout/default.php', 'Existing override');
        $command = $this->getMockBuilder(CopyLayoutsCommand::class)
            ->onlyMethods(['applicationTemplatesPath'])->getMock();
        $command->expects($this->once())->method('applicationTemplatesPath')->willReturn($path);
        $output = new StubConsoleOutput();
        $io = new ConsoleIo($output, $output);
        try {
            $this->assertSame(0, $command->run([], $io));
            $this->assertSame('Existing override', file_get_contents($path . 'plugin/TailwindUi/layout/default.php'));
            $this->assertFileExists($path . 'plugin/TailwindUi/layout/ktui.php');
            $this->assertFileExists($path . 'plugin/TailwindUi/element/flash/default.php');
            $this->assertFileDoesNotExist($path . 'layout/default.php');
        } finally {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
            }
            rmdir($path);
        }
    }

    public function testMissingSourcesReturnErrorWithoutWritingFiles(): void
    {
        $command = $this->getMockBuilder(CopyLayoutsCommand::class)
            ->onlyMethods(['applicationTemplatesPath', 'sourceTemplatesPath'])->getMock();
        $command->expects($this->once())->method('applicationTemplatesPath')->willReturn(TMP . 'tailwind-missing-dest/');
        $command->expects($this->once())->method('sourceTemplatesPath')->willReturn(TMP . 'tailwind-missing-source/');
        $io = $this->getMockBuilder(ConsoleIo::class)->onlyMethods(['createFile', 'error'])->getMock();
        $io->expects($this->never())->method('createFile');
        $io->expects($this->exactly(3))->method('error');
        $this->assertSame(CopyLayoutsCommand::CODE_ERROR, $command->run([], $io));
    }
}
