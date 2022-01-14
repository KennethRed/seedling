<?php

namespace Seedling\Abstracts;

use Seedling\SeedlingConfigurator;
use WP_CLI;

abstract class SeederIndexAbstract
{
    public SeedlingConfigurator $seedlingConfigurator;

    private array $seederInstances;

    public function seeders(): array
    {
        return [];
    }

    public function buildApplication(): void
    {
        if (class_exists('WP_CLI')) {
            if (count($this->commandsBefore() == 0)) {
                WP_CLI::log('No commandsBefore found, Skipping...');
            } else {
                WP_CLI::log('running commands before...');
                foreach (self::commandsBefore() as $command) {
                    WP_CLI::runcommand($command);
                }
            }

            if (count($this->commands() == 0)) {
                WP_CLI::log('No manual commands found, commencing automatic mode...');
            } else {
                WP_CLI::log('Manual commands found, using manual commands from commands() instead of automatic mode');
                foreach (self::commands() as $command) {
                    WP_CLI::runcommand($command);
                }
            }

            if (count($this->commandsAfter() == 0)) {
                WP_CLI::log('No commandsAfter found, Skipping...');
            } else {
                WP_CLI::log('running commands after...');
                foreach (self::commandsAfter() as $command) {
                    WP_CLI::runcommand($command);
                }
            }

            WP_CLI::log('all available build commands are done. Exiting.');
        }
    }

    public function __construct()
    {
        $this->seedlingConfigurator = $this->seedlingConfigurator();

        $this->initializeSeeders();

        if (class_exists('WP_CLI')) {
            WP_CLI::add_command("seed build-application", array($this, 'buildApplication'));
        }
    }

    protected function initializeSeeders()
    {
        if(count($this->seeders()) > 0 ){
            foreach($this->seeders() as $seeder){
                $this->seederInstances[] = new $seeder($this->seedlingConfigurator);
            }
        }
    }

    protected function seedlingConfigurator(): SeedlingConfigurator
    {
        return new SeedlingConfigurator($this->config());
    }

    function commandsBefore(): array
    {
        return [];
    }

    function commands(): array
    {
        return [];
    }

    function commandsAfter(): array
    {
        return [];
    }

    function config(): array
    {
        return [
            'seedlingApp' => [
                'seedlingDefaultMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
                'defaultLimit' => 10,
            ],
            'models' => [
                'posts' => [
                    'enabled' => false,
                ],
                'projects' => [
                    'limit' => 10,
                    'seedlingMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
                ],
                'example-custom' => [
                    'limit' => 15,
                    'seedlingMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
                ]
            ],
            'taxonomies' => [
                'blacklisted' => []
            ],
            'options' => [
                'seedlingMode' => 'fill_all', // fill_all | fill_required | fill_random_respect_required, | fill_random_disregard_required
            ]
        ];
    }
}
