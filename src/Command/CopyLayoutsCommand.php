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
        $pluginPath = Plugin::path('TailwindUi');
        $appTemplates = ROOT . DS . 'templates' . DS;

        $filesToCopy = [
            $pluginPath . 'templates' . DS . 'layout' . DS . 'default.php' => $appTemplates . 'layout' . DS . 'default.php',
            $pluginPath . 'templates' . DS . 'element' . DS . 'flash' . DS . 'default.php' => $appTemplates . 'element' . DS . 'flash' . DS . 'default.php',
        ];

        foreach ($filesToCopy as $source => $dest) {
            $destDir = dirname($dest);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
                $io->out('<info>Created directory:</info> ' . $destDir);
            }

            if (file_exists($dest)) {
                $io->warning('File already exists, skipping: ' . $dest);
                continue;
            }

            if (!file_exists($source)) {
                $io->error('Source file not found: ' . $source);
                continue;
            }

            copy($source, $dest);
            $io->out('<success>Copied:</success> ' . $dest);
        }

        $io->out('');
        $io->out('<info>Done!</info> Layout files copied to your application templates directory.');

        return static::CODE_SUCCESS;
    }
}
