<?php

namespace Src\Controller;

use PDOException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Src\Model\WorkflowModel;

class WorkflowController extends RestController
{
    private $wfModel = null;

    public function __construct(WorkflowModel $wfModel)
    {
        $this->wfModel = $wfModel;
    }

    public function listWFs($response)
    {
        $json = json_encode($this->wfModel->listWFs());
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newWF($request, $response)
    {
        try {
            $wfid = $this->wfModel->newWF($request->getParsedBody());
            $response->getBody()->write(json_encode(["wfid" => $wfid]));
            $response->withHeader("Content-Type", "application/json; charset=UTF-8");
            return $response->withStatus(201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        }
    }

    public function findWF($request, $response, $wfid)
    {
        $wf = $this->wfModel->findWF($wfid);
        if (!$wf) {
            throw new HttpNotFoundException($request);
        }
        $json = json_encode($wf);
        $eTag = $this->getCRC32C($json);
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8")
                        ->withHeader("ETag", $eTag);
    }

    public function updateWF($request, $response, $wfid)
    {
        $wf = $this->wfModel->findWF($wfid);
        if (!$wf) {
            throw new HttpNotFoundException($request);
        }
        if ($this->optimisticLockFailure($request, json_encode($wf))) {
            return $response->withStatus(412);
        }
        try {
            $this->wfModel->updateWF($wfid, $request->getParsedBody());
            return $response->withStatus(204);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        }
    }

    public function patchWFName($request, $response, $wfid)
    {
        $wf = $this->wfModel->findWF($wfid);
        if (!$wf) {
            throw new HttpNotFoundException($request);
        }
        if ($this->optimisticLockFailure($request, json_encode($wf))) {
            return $response->withStatus(412);
        }

        try {
            $this->wfModel->patchWFName($wfid, $request->getParsedBody());
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
