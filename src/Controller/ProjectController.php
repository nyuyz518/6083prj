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

    public function __construct(ProjectModel $projectModel)
    {
        $this->projectModel = $projectModel;
    }

    public function listProjects($response)
    {
        $response->getBody()->write(json_encode($this->projectModel->findAll()));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function getProject($request, $response, $pid){
        $p = $this->projectModel->getProject($pid);
        if(!$p){
            throw new HttpNotFoundException($request);
        }
        $json = json_encode($p);
        $eTag = $this->getCRC32C($json);
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8")
                        ->withHeader("ETag", $eTag);
    }

    public function newProject($request, $response){
        $jwt = $request->getAttribute("jwt");
        try{
            $pid = $this->projectModel->create($jwt['uid'], $request->getParsedBody());
            $response->getBody()->write(json_encode(["pid" => $pid]));
            return $response->withStatus(201)->withHeader("Content-Type", "application/json; charset=UTF-8");
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

    public function updateProject($request, $response, $pid){
        $jwt = $request->getAttribute("jwt");
        if(!$this->projectModel->isOwner($jwt['uid'], $pid)){
            throw new HttpUnauthorizedException($request);
        }

        $p = $this->projectModel->getProject($pid);
        if(!$p){
            throw new HttpNotFoundException($request);
        }

        if($this->optimisticLockFailure($request, json_encode($p))){
            return $response->withStatus(412);
        }

        try{
            $this->projectModel->update($pid, $request->getParsedBody());
        } catch (PDOException $e){
            if($e->getCode() == 23000){
                throw new HttpBadRequestException($request, $e);
            }else {
                throw $e;
            }
        }
        return $response->withStatus(204);
    }

    public function patchProject($request, $response, $pid){
        $jwt = $request->getAttribute("jwt");
        if(!$this->projectModel->isOwner($jwt['uid'], $pid)){
            throw new HttpUnauthorizedException($request);
        }

        $p = $this->projectModel->getProject($pid);
        if(!$p){
            throw new HttpNotFoundException($request);
        }

        if($this->optimisticLockFailure($request, json_encode($p))){
            return $response->withStatus(412);
        }
                
        try{
            $this->projectModel->patch($pid, $request->getParsedBody());
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
