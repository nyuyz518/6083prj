<?php
namespace Src\Controller;

use Src\Repository\TaskRepo;

class TaskController {
    private $db;
    private $taskRepo;

    public function __construct($db, $app){
        $this->db = $db;
        $this->taskRepo = new TaskRepo($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app) {
        $app->get('/task/{id}', function ($request, $response, $args){
            // Show book identified by $args['id']
            $payload = $this->getTask($args['id']);
            $response->getBody()->write($payload);
            return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
        });
    }

    public function getTask($id){
        $result = $this->taskRepo->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        return json_encode($result);
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}