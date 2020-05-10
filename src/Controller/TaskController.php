<?php

namespace Src\Controller;

use PDOException;
use Slim\Exception\HttpBadRequestException;
use Src\Model\TaskModel;

use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;

class TaskController extends RestController
{
    private $taskModel = null;

    public function __construct(TaskModel $taskModel)
    {
        $this->taskModel = $taskModel;
    }

    public function getTask($request, $response, $tid)
    {
        $t = $this->taskModel->find($tid);
        if (!$t) {
            throw new HttpNotFoundException($request);
        }
        $json = json_encode($t);
        $eTag = $this->getCRC32C($json);
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8")
            ->withHeader("ETag", $eTag);
    }

    public function listTasks($request, $response)
    {
        $query = $request->getQueryParams();
        $response->getBody()->write(json_encode($this->taskModel->findAll($query)));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newTask($request, $response)
    {
        $jwt = $request->getAttribute("jwt");
        try {
            $this->taskModel->create($jwt['uid'], $request->getParsedBody());
            return $response->withStatus(201);
        } catch (PDOException $e){
            if($e->getCode() == 23000){
                throw new HttpBadRequestException($request, $e);
            }else {
                throw $e;
            }
        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e);
        }
    }

    public function updateTask($request, $response, $tid)
    {
        $jwt = $request->getAttribute("jwt");
        if (!$this->taskModel->isAssigned($jwt['uid'], $tid)) {
            throw new HttpUnauthorizedException($request);
        }

        $t = $this->taskModel->find($tid);
        if (!$t) {
            throw new HttpNotFoundException($request);
        }

        if ($this->optimisticLockFailure($request, json_encode($t))) {
            return $response->withStatus(412);
        }

        try {
            $this->taskModel->update($tid, $request->getParsedBody());
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        }
        return $response->withStatus(204);
    }

    public function patchTask($request, $response, $tid)
    {
        $jwt = $request->getAttribute("jwt");
        if (!$this->taskModel->isAssigned($jwt['uid'], $tid)) {
            throw new HttpUnauthorizedException($request);
        }

        $t = $this->taskModel->find($tid);
        if (!$t) {
            throw new HttpNotFoundException($request);
        }

        if ($this->optimisticLockFailure($request, json_encode($t))) {
            return $response->withStatus(412);
        }

        try {
            $this->taskModel->patch($tid, $request->getParsedBody());
            return $response->withStatus(204);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e);
        }
    }

    public function getTaskStatusHistory($response, $tid)
    {
        $response->getBody()->write(json_encode($this->taskModel->listStatusHistory($tid)));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newTaskStatus($request, $response, $tid)
    {
        $this->taskModel->newStatusForTask($tid, $request->getParsedBody());
        return $response->withStatus(201);
    }
}
