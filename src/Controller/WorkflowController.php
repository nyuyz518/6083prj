<?php

namespace Src\Controller;

use PDOException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Src\Model\WorkflowModel;

class WorkflowController extends RestController
{
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

    public function listWFs($request, $response, $args)
    {
        $json = json_encode($this->wfModel->listWFs());
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newWF($request, $response, $args)
    {
        try {
            $this->wfModel->newWF($request->getParsedBody());
            return $response->withStatus(201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        }
    }

    public function findWF($request, $response, $args)
    {
        $wf = $this->wfModel->findWF($args["wfid"]);
        if (!$wf) {
            throw new HttpNotFoundException($request);
        }
        $json = json_encode($wf);
        $eTag = $this->getCRC32C($json);
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8")
                        ->withHeader("ETag", $eTag);
    }

    public function updateWF($request, $response, $args)
    {
        $wf = $this->wfModel->findWF($args["wfid"]);
        if (!$wf) {
            throw new HttpNotFoundException($request);
        }
        if ($this->optimisticLockFailure($request, json_encode($wf))) {
            return $response->withStatus(412);
        }
        try {
            $this->wfModel->updateWF($args["wfid"], $request->getParsedBody());
            return $response->withStatus(204);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        }
    }

    public function patchWFName($request, $response, $args)
    {
        $wf = $this->wfModel->findWF($args["wfid"]);
        if (!$wf) {
            throw new HttpNotFoundException($request);
        }
        if ($this->optimisticLockFailure($request, json_encode($wf))) {
            return $response->withStatus(412);
        }

        try {
            $this->wfModel->patchWFName($args["wfid"], $request->getParsedBody());
            return $response->withStatus(204);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        }
    }
}
