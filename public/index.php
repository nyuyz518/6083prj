<?php
require "../bootstrap.php";

use Slim\Factory\AppFactory;

use Src\Controller\TaskController;
use Src\Controller\UserController;
use Src\Controller\ProjectController;


$appContext = AppFactory::create();
$appContext->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => getenv("JWT_SECRET"),
    "attribute" => "jwt",
    "path" => ["/user", "/task", "/project"],
    "ignore" => "/user/auth"
]));
$appContext->addRoutingMiddleware();
$appContext->addBodyParsingMiddleware();
$errorMiddleware = $appContext->addErrorMiddleware(true, true, true);

$taskController = new TaskController($dbConnection, $appContext);
$userController = new UserController($dbConnection, $appContext);
$projectController = new ProjectController($dbConnection, $appContext);


$appContext->run();
