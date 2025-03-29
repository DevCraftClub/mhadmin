<?php declare(strict_types=1);











namespace Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Composer\Console\Input\InputOption;
use Composer\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;




class ScriptAliasCommand extends BaseCommand
{

private $script;

private $description;

public function __construct(string $script, ?string $description)
{
$this->script = $script;
$this->description = $description ?? 'Runs the '.$script.' script as defined in composer.json';

parent::__construct();
}

protected function configure(): void
{
$this
->setName($this->script)
->setDescription($this->description)
->setDefinition([
new InputOption('dev', null, InputOption::VALUE_NONE, 'Sets the dev mode.'),
new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables the dev mode.'),
new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
])
->setHelp(
<<<EOT
The <info>run-script</info> command runs scripts defined in composer.json:

<info>php composer.phar run-script post-update-cmd</info>

Read more at https://getcomposer.org/doc/03-cli.md#run-script
EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output): int
{
$composer = $this->requireComposer();

$args = $input->getArguments();

return $composer->getEventDispatcher()->dispatchScript($this->script, $input->getOption('dev') || !$input->getOption('no-dev'), $args['args']);
}
}
