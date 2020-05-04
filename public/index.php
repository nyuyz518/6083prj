<?php
require "../bootstrap.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Src\Controller\TaskController;
use Src\Controller\UserController;


$appContext = AppFactory::create();
$appContext->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => getenv("JWT_SECRET"),
    "path" => "/task"
]));
$appContext->addRoutingMiddleware();
$appContext->addBodyParsingMiddleware();
$errorMiddleware = $appContext->addErrorMiddleware(true, true, true);

$taskController = new TaskController($dbConnection, $appContext);
$userController = new UserController($dbConnection, $appContext);


$appContext->run();