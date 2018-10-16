<?php
namespace dcgen\Command;

use dcgen\ElementRemover;
use dcgen\EnvironmentLoader;
use dcgen\EnvironmentSubstitutor;
use dcgen\TemplateLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('generate docker-compose config from YAML')
            ->setDefinition([
                new InputArgument('template', InputArgument::OPTIONAL, 'template file'),
                new InputOption('env', 'e', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'env'),
                new InputOption('env-file', null, InputOption::VALUE_OPTIONAL, 'environment file'),
                new InputOption('exclude', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'keys to be excluded from output'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $excluded = $input->getOption('exclude');
        $env = [];
        foreach ($input->getOption('env') as $pair) {
            list($k, $v) = explode('=', $pair);
            $env[$k] = $v;
        }
        $envFile = $input->getOption('env-file');
        if ($envFile) {
            $loader = new EnvironmentLoader($envFile);
            $env += $loader->load();
        }
        $templateFile = $input->getArgument('template');
        $templateLoader = new TemplateLoader($templateFile);
        $template = $templateLoader->load($input);
        $remover = new ElementRemover();
        $remover->remove($template, $excluded);
        $yml = Yaml::dump($template, 10, 2);
        $misses = [];
        $substitutor = new EnvironmentSubstitutor();
        $yml = $substitutor->substitute($yml, $env, $misses);
        $output->writeln($yml);
        if (count($misses) > 0) {
            $error = $output instanceof ConsoleOutput ? $output->getErrorOutput() : $output;
            $error->writeLn(sprintf('WARNING: %d keys were not defined: %s', count($misses), implode(', ', $misses)));
        }
    }
}
