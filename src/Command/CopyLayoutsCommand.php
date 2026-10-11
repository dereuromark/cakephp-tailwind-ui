<?php

declare(strict_types=1);

namespace TailwindUi\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;

class CopyLayoutsCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'tailwind_ui copy_layouts';
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Copy TailwindUi layout and elements to the application templates directory.');

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $pluginTemplates = $this->sourceTemplatesPath();
        $appTemplates = $this->applicationTemplatesPath() . 'plugin' . DS . 'TailwindUi' . DS;
        $failed = false;

        $filesToCopy = [
            $pluginTemplates . 'layout' . DS . 'default.php' => $appTemplates . 'layout' . DS . 'default.php',
            $pluginTemplates . 'layout' . DS . 'ktui.php' => $appTemplates . 'layout' . DS . 'ktui.php',
            $pluginTemplates . 'element' . DS . 'flash' . DS . 'default.php' => $appTemplates . 'element' . DS . 'flash' . DS . 'default.php',
        ];

        foreach ($filesToCopy as $source => $dest) {
            if (file_exists($dest)) {
                $io->warning('File already exists, skipping: ' . $dest);

                continue;
            }

            if (!is_file($source) || !is_readable($source)) {
                $io->error('Source file missing or unreadable: ' . $source);
                $failed = true;

                continue;
            }

            $contents = @file_get_contents($source);
            if ($contents === false) {
                $io->error('Could not read source file: ' . $source);
                $failed = true;

                continue;
            }
            if (!$io->createFile($dest, $contents)) {
                $failed = true;
            }
        }

        if ($failed) {
            return static::CODE_ERROR;
        }

        $io->out('');
        $io->out('<info>Done!</info> Layout files copied or existing files preserved.');

        return static::CODE_SUCCESS;
    }

    protected function applicationTemplatesPath(): string
    {
        return ROOT . DS . 'templates' . DS;
    }

    protected function sourceTemplatesPath(): string
    {
        return Plugin::path('TailwindUi') . 'templates' . DS;
    }
}
