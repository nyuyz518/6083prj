<?php

namespace Src\Controller;

use PDOException;
use Src\Model\ProjectModel;

use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class ProjectController extends RestController
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
        $app->post('/project', array($this, "newProject"));
        $app->get('/project/{pid}', array($this, "getProject"));
        $app->put('/project/{pid}', array($this, "updateProject"));
        $app->patch('/project/{pid}', array($this, "patchProject"));
    }

    public function listProjects($request, $response, $args)
    {
        $response->getBody()->write(json_encode($this->projectModel->findAll()));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function getProject($request, $response, $args){
        $p = $this->projectModel->getProject($args["pid"]);
        if(!$p){
            throw new HttpNotFoundException($request);
        }
        $json = json_encode($p);
        $eTag = $this->getCRC32C($json);
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8")
                        ->withHeader("ETag", $eTag);
    }

    public function newProject($request, $response, $args){
        $jwt = $request->getAttribute("jwt");
        try{
            $this->projectModel->create($jwt['uid'], $request->getParsedBody());
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

    public function updateProject($request, $response, $args){
        $jwt = $request->getAttribute("jwt");
        if(!$this->projectModel->isOwner($jwt['uid'], $args["pid"])){
            throw new HttpUnauthorizedException($request);
        }

        $p = $this->projectModel->getProject($args["pid"]);
        if(!$p){
            throw new HttpNotFoundException($request);
        }

        if($this->optimisticLockFailure($request, json_encode($p))){
            return $response->withStatus(412);
        }

        try{
            $this->projectModel->update($args["pid"], $request->getParsedBody());
        } catch (PDOException $e){
            if($e->getCode() == 23000){
                throw new HttpBadRequestException($request, $e);
            }else {
                throw $e;
            }
        }
        return $response->withStatus(204);
    }

    public function patchProject($request, $response, $args){
        $jwt = $request->getAttribute("jwt");
        if(!$this->projectModel->isOwner($jwt['uid'], $args["pid"])){
            throw new HttpUnauthorizedException($request);
        }

        $p = $this->projectModel->getProject($args["pid"]);
        if(!$p){
            throw new HttpNotFoundException($request);
        }

        if($this->optimisticLockFailure($request, json_encode($p))){
            return $response->withStatus(412);
        }
                
        try{
            $this->projectModel->patch($args["pid"], $request->getParsedBody());
            return $response->withStatus(204);
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

}
