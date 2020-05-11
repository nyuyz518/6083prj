<?php
require "../bootstrap.php";

use Src\Controller\TaskController;
use Src\Controller\UserController;
use Src\Controller\ProjectController;
use Src\Controller\StatusController;
use Src\Controller\WorkflowController;

$container = $containerBuilder->build();
$app = \DI\Bridge\Slim\Bridge::create($container);

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => getenv("JWT_SECRET"),
    "attribute" => "jwt",
    "path" => ["/task", "/project"]
]));
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->options('/{routes:.+}', function ($response) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', getenv('CLIENT_ORIGIN'))
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


$app->get('/user', [UserController::class, "listUsers"]);
$app->get('/user/{id}', [UserController::class, "getUser"]);
$app->put('/user/{id}', [UserController::class, "updateUser"]);
$app->post('/user', [UserController::class, "newUser"]);
$app->post('/auth', [UserController::class, "authUser"]);

$app->get('/project', [ProjectController::class, "listProjects"]);
$app->post('/project', [ProjectController::class, "newProject"]);
$app->get('/project/{pid}', [ProjectController::class, "getProject"]);
$app->put('/project/{pid}', [ProjectController::class, "updateProject"]);
$app->patch('/project/{pid}', [ProjectController::class, "patchProject"]);

$app->get('/task', [TaskController::class, "listTasks"]);
$app->post('/task', [TaskController::class, "newTask"]);
$app->get('/task/{tid}', [TaskController::class, "getTask"]);
$app->put('/task/{tid}', [TaskController::class, "updateTask"]);
$app->patch('/task/{tid}', [TaskController::class, "patchTask"]);
$app->get('/task/{tid}/status', [TaskController::class, "getTaskStatusHistory"]);
$app->post('/task/{tid}/status', [TaskController::class, "newTaskStatus"]);

$app->get('/status', [StatusController::class, "listStatus"]);
$app->post('/status', [StatusController::class, "newStatus"]);
$app->get('/status/{sid}', [StatusController::class, "getStatus"]);

$app->get('/workflow', [WorkflowController::class, "listWFs"]);
$app->post('/workflow', [WorkflowController::class, "newWF"]);
$app->get('/workflow/{wfid}', [WorkflowController::class, "findWF"]);
$app->put('/workflow/{wfid}', [WorkflowController::class, "updateWF"]);
$app->patch('/workflow/{wfid}', [WorkflowController::class, "patchWFName"]);


$app->run();
