<?php

namespace Src\Controller;

use Src\Model\UserModel;

use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpBadRequestException;

use \Firebase\JWT\JWT;

class UserController
{
    private $userModel;

    public function __construct($db, $app)
    {
        $this->userModel = new UserModel($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app)
    {
        $app->get('/user', array($this, "listUsers"));
        $app->get('/user/{id}', array($this, "getUser"));
        $app->put('/user/{id}', array($this, "updateUser"));
        $app->post('/user', array($this, "newUser"));
        $app->post('/user/auth', array($this, "authUser"));
    }

    public function listUsers($request, $response, $args)
    {
        $query = $request->getQueryParams();
        $response->getBody()->write(json_encode($this->userModel->findAll($query)));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function getUser($request, $response, $args)
    {
        $result = $this->userModel->find($args['id']);
        if (!$result) {
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newUser($request, $response, $args)
    {
        $rc = $this->userModel->create($request->getParsedBody());
        if ($rc == 0) {
            throw new HttpBadRequestException($request);
        }
        return $response->withStatus(201);
    }

    public function updateUser($request, $response, $args)
    {
        $jwt = $request->getAttribute("jwt");
        $uid = $args['id'];
        if ($jwt['uid'] != $uid) {
            throw new HttpUnauthorizedException($request);
        }
        $rc = $this->userModel->update($uid, $request->getParsedBody());
        return $response->withStatus(204);
    }

    public function authUser($request, $response, $args)
    {
        $body = $request->getParsedBody();
        if (!$body || !array_key_exists("uname", $body) || !array_key_exists("pwd", $body)) {
            throw new HttpBadRequestException($request);
        }

        $uid = $body["uname"];
        $pwd = $body["pwd"];

        if ($this->userModel->auth($uid, $pwd)) {
            $secret = getenv("JWT_SECRET");
            $payload = [
                "uid" => $uid
            ];
            $token = JWT::encode($payload, $secret, "HS256");
            $response->getBody()->write(json_encode(["jwt" => $token]));
            return $response->withStatus(200);
        } else {
            throw new HttpUnauthorizedException($request);
        }
    }
}
