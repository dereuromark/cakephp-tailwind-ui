<?php

declare(strict_types=1);

namespace TailwindUi\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class InstallCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'tailwind_ui install';
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Show setup instructions for TailwindUi plugin.');

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $io->out('<info>TailwindUi Plugin Setup</info>');
        $io->out('');
        $io->out('1. Load the plugin in your Application::bootstrap():');
        $io->out('   $this->addPlugin(\'TailwindUi\');');
        $io->out('');
        $io->out('2. Configure your AppView to load helpers:');
        $io->out('   $this->loadHelper(\'Form\', [\'className\' => \'TailwindUi.Form\']);');
        $io->out('   $this->loadHelper(\'Html\', [\'className\' => \'TailwindUi.Html\']);');
        $io->out('   $this->loadHelper(\'Flash\', [\'className\' => \'TailwindUi.Flash\']);');
        $io->out('   $this->loadHelper(\'Paginator\', [\'className\' => \'TailwindUi.Paginator\']);');
        $io->out('   $this->loadHelper(\'Breadcrumbs\', [\'className\' => \'TailwindUi.Breadcrumbs\']);');
        $io->out('');
        $io->out('3. (Optional) Copy layouts to your application:');
        $io->out('   bin/cake tailwind_ui copy_layouts');
        $io->out('');
        $io->out('4. (Optional) Switch to KTUI class map:');
        $io->out('   Configure::write(\'TailwindUi.classMap\', \'ktui\');');
        $io->out('');

        return static::CODE_SUCCESS;
    }
}
