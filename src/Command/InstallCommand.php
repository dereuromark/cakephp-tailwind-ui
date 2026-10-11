<?php

declare(strict_types=1);

namespace TailwindUi\Command;

use Cake\Command\Command;
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
    public function execute(): ?int
    {
        $args = $this->args;
        $io = $this->io;
        $io->out('<info>TailwindUi Plugin Setup</info>');
        $io->out('');
        $io->out('1. Load the plugin in your Application::bootstrap():');
        $io->out('   $this->addPlugin(\'TailwindUi\');');
        $io->out('');
        $io->out('2. Add UiViewTrait to AppView and call initializeUi() after parent::initialize():');
        $io->out('   use \\TailwindUi\\View\\UiViewTrait;');
        $io->out('   $this->initializeUi();');
        $io->out('   See https://dereuromark.github.io/cakephp-tailwind-ui/guide/getting-started');
        $io->out('');
        $io->out('3. Load a compiled Tailwind CSS / DaisyUI stylesheet in your layout.');
        $io->out('   See https://dereuromark.github.io/cakephp-tailwind-ui/guide/css');
        $io->out('');
        $io->out('4. (Optional) Copy layouts to your application:');
        $io->out('   bin/cake tailwind_ui copy_layouts');
        $io->out('');
        $io->out('5. (Optional) Switch to KTUI class map:');
        $io->out('   Configure::write(\'TailwindUi.classMap\', \'ktui\');');
        $io->out('');

        return static::CODE_SUCCESS;
    }
}
