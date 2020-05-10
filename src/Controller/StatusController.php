<?php

namespace Src\Controller;

use PDOException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Src\Model\StatusModel;

class StatusController
{
    private $statusModel = null;

    public function __construct(StatusModel $statusModel)
    {
        $this->statusModel = $statusModel;
    }

    public function listStatus($response)
    {
        $json = json_encode($this->statusModel->listAllStatus());
        $response->getBody()->write($json);
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }
    public function newStatus($request, $response)
    {
        try{
            $this->statusModel->newStatus($request->getParsedBody());
        } catch (PDOException $e){
            if($e->getCode() == 23000){
                throw new HttpBadRequestException($request, $e);
            }else {
                throw $e;
            }
        }
        return $response->withStatus(201);
    }
    public function getStatus($request, $response, $sid)
    {
        $s = $this->statusModel->getStatus($sid);
        if(!$s){
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write( json_encode($s));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }
}
