<?php
namespace Src\Controller;

use Src\Repository\TaskRepo;
use Slim\Exception\HttpNotFoundException;

class TaskController {
    private $taskRepo;

    public function __construct($db, $app){
        $this->taskRepo = new TaskRepo($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app) {
        $app->get('/task/{id}', array($this, "getTask"));
    }

    public function getTask($request, $response, $args){
        $result = $this->taskRepo->find($args['id']);
        if (! $result) {
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");;
    }

}