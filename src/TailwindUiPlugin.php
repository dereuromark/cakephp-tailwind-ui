<?php
declare(strict_types=1);

namespace TailwindUi;

use TailwindUi\Command\CopyLayoutsCommand;
use TailwindUi\Command\InstallCommand;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;

class TailwindUiPlugin extends BasePlugin
{
    protected ?string $name = 'TailwindUi';
    protected bool $bootstrapEnabled = false;
    protected bool $middlewareEnabled = false;
    protected bool $servicesEnabled = false;
    protected bool $routesEnabled = false;
    protected bool $eventsEnabled = false;

    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands
            ->add('tailwind_ui install', InstallCommand::class)
            ->add('tailwind_ui copy_layouts', CopyLayoutsCommand::class);
    }
}
