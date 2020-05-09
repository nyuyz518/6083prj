<?php
namespace Src\Controller;

use Slim\Exception\HttpNotFoundException;
use Src\Model\WorkflowModel;

class WorkflowController {
    private $wfModel = null;

    public function __construct($db, $app)
    {
        $this->wfModel = new WorkflowModel($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app)
    {
        $app->get('/workflow', array($this, "listWFs"));
        $app->post('/workflow', array($this, "newWF"));
        $app->get('/workflow/{wfid}', array($this, "findWF"));
        $app->put('/workflow/{wfid}', array($this, "updateWF"));
        $app->patch('/workflow/{wfid}', array($this, "patchWFName"));
    }

    public function listWFs($request, $response, $args){
        $json = json_encode($this->wfModel->listWFs());
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newWF($request, $response, $args){
        $this->wfModel->newWF($request->getParsedBody());
        return $response->withStatus(201);
    }

    public function findWF($request, $response, $args){
        $wf = $this->wfModel->findWF($args["wfid"]);
        if(!$wf){
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write(json_encode($wf));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function updateWF($request, $response, $args){
        $this->wfModel->updateWF($args["wfid"], $request->getParsedBody());
        return $response->withStatus(204);
    }

    public function patchWFName($request, $response, $args){
        $this->wfModel->patchWFName($args["wfid"], $request->getParsedBody());
        return $response->withStatus(204);
    }
}