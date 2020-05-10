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

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function listUsers($request, $response)
    {
        $query = $request->getQueryParams();
        $response->getBody()->write(json_encode($this->userModel->findAll($query)));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function getUser($request, $response, $id)
    {
        $result = $this->userModel->find($id);
        if (!$result) {
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newUser($request, $response)
    {
        $rc = $this->userModel->create($request->getParsedBody());
        if ($rc == 0) {
            throw new HttpBadRequestException($request);
        }
        return $response->withStatus(201);
    }

    public function updateUser($request, $response, $id)
    {
        $jwt = $request->getAttribute("jwt");
        $uid = $id;
        if ($jwt['uid'] != $uid) {
            throw new HttpUnauthorizedException($request);
        }
        $rc = $this->userModel->update($uid, $request->getParsedBody());
        return $response->withStatus(204);
    }

    public function authUser($request, $response)
    {
        $body = $request->getParsedBody();
        if (!$body || !array_key_exists("uname", $body) || !array_key_exists("pwd", $body)) {
            throw new HttpBadRequestException($request);
        }

        $uname = $body["uname"];
        $pwd = $body["pwd"];

        if ($uid = $this->userModel->auth($uname, $pwd)) {
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
