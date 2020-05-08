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

    public function __construct($db, $app)
    {
        $this->taskModel = new TaskModel($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app)
    {
        $app->get('/task', array($this, "listTasks"));
        $app->post('/task', array($this, "newTask"));
        $app->get('/task/{tid}', array($this, "getTask"));
        $app->put('/task/{tid}', array($this, "updateTask"));
        $app->patch('/task/{tid}', array($this, "patchTask"));
        $app->get('/task/{tid}/status', array($this, "getTaskStatusHistory"));
        $app->post('/task/{tid}/status', array($this, "newTaskStatus"));
    }

    public function getTask($request, $response, $args)
    {
        $t = $this->taskModel->find($args['tid']);
        if (!$t) {
            throw new HttpNotFoundException($request);
        }
        $json = json_encode($t);
        $eTag = $this->getCRC32C($json);
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8")
            ->withHeader("ETag", $eTag);
    }

    public function listTasks($request, $response, $args)
    {
        $query = $request->getQueryParams();
        $response->getBody()->write(json_encode($this->taskModel->findAll($query)));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newTask($request, $response, $args)
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

    public function updateTask($request, $response, $args)
    {
        $jwt = $request->getAttribute("jwt");
        if (!$this->taskModel->isAssigned($jwt['uid'], $args["tid"])) {
            throw new HttpUnauthorizedException($request);
        }

        $t = $this->taskModel->find($args["tid"]);
        if (!$t) {
            throw new HttpNotFoundException($request);
        }

        if ($this->optimisticLockFailure($request, json_encode($t))) {
            return $response->withStatus(412);
        }

        try {
            $this->taskModel->update($args["tid"], $request->getParsedBody());
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpBadRequestException($request, $e);
            } else {
                throw $e;
            }
        }
        return $response->withStatus(204);
    }

    public function patchTask($request, $response, $args)
    {
        $jwt = $request->getAttribute("jwt");
        if (!$this->taskModel->isAssigned($jwt['uid'], $args["tid"])) {
            throw new HttpUnauthorizedException($request);
        }

        $t = $this->taskModel->find($args["tid"]);
        if (!$t) {
            throw new HttpNotFoundException($request);
        }

        if ($this->optimisticLockFailure($request, json_encode($t))) {
            return $response->withStatus(412);
        }

        try {
            $this->taskModel->patch($args["tid"], $request->getParsedBody());
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

    public function getTaskStatusHistory($request, $response, $args)
    {
        return $response->withStatus(201);
    }

    public function newTaskStatus($request, $response, $args)
    {
        return $response->withStatus(201);
    }
}
