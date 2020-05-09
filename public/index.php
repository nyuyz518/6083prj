<?php
require "../bootstrap.php";

use Slim\Factory\AppFactory;

use Src\Controller\TaskController;
use Src\Controller\UserController;
use Src\Controller\ProjectController;
use Src\Controller\StatusController;
use Src\Controller\WorkflowController;

$appContext = AppFactory::create();
$appContext->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => getenv("JWT_SECRET"),
    "attribute" => "jwt",
    "path" => ["/task", "/project"]
]));
$appContext->addRoutingMiddleware();
$appContext->addBodyParsingMiddleware();
$errorMiddleware = $appContext->addErrorMiddleware(true, true, true);

$taskController = new TaskController($dbConnection, $appContext);
$userController = new UserController($dbConnection, $appContext);
$projectController = new ProjectController($dbConnection, $appContext);
$statusController = new StatusController($dbConnection, $appContext);
$wfController = new WorkflowController($dbConnection, $appContext);



$appContext->run();
