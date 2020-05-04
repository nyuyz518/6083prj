<?php
require "../bootstrap.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Src\Controller\TaskController;

$appContext = AppFactory::create();

$taskController = new TaskController($dbConnection, $appContext);

$appContext->run();