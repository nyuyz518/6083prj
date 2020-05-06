<?php

namespace Src\Controller;

use Src\Model\TaskModel;

use Slim\Exception\HttpNotFoundException;

class TaskController
{
    private $taskModel = null;

    public function __construct($db, $app)
    {
        $this->taskModel = new TaskModel($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app)
    {
        $app->get('/task/{id}', array($this, "getTask"));
        $app->get('/task', array($this, "listTasks"));
    }

    public function getTask($request, $response, $args)
    {
        $result = $this->taskModel->find($args['id']);
        if (!$result) {
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");;
    }

    public function listTasks($request, $response, $args)
    {
        $query = $request->getQueryParams();
        $response->getBody()->write(json_encode($this->taskModel->findAll($query)));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }
}
