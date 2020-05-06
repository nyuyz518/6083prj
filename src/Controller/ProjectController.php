<?php

namespace Src\Controller;

use Src\Model\ProjectModel;

use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpBadRequestException;

class ProjectController
{
    private $projectModel = null;

    public function __construct($db, $app)
    {
        $this->projectModel = new ProjectModel($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app)
    {
        $app->get('/project', array($this, "listProjects"));
    }

    public function listProjects($request, $response, $args)
    {
        $response->getBody()->write(json_encode($this->projectModel->findAll()));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }
}
