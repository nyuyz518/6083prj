<?php

namespace Src\Controller;

use Slim\Exception\HttpNotFoundException;
use Src\Model\StatusModel;

class StatusController
{
    private $statusModel = null;

    public function __construct($db, $app)
    {
        $this->statusModel = new StatusModel($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app)
    {
        $app->get('/status', array($this, "listStatus"));
        $app->post('/status', array($this, "newStatus"));
        $app->get('/status/{sid}', array($this, "getStatus"));
    }

    public function listStatus($request, $response, $args)
    {
        $json = json_encode($this->statusModel->listAllStatus());
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }
    public function newStatus($request, $response, $args)
    {
        $this->statusModel->newStatus($request->getParsedBody());
        return $response->withStatus(201);
    }
    public function getStatus($request, $response, $args)
    {
        $s = $this->statusModel->getStatus($args["sid"]);
        if(!$s){
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write( json_encode($s));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }
}
