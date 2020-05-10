<?php
require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use Dotenv\Dotenv;

use Src\System\DatabaseConnector;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    DatabaseConnector::class => DI\create(DatabaseConnector::class)->constructor()
]);
