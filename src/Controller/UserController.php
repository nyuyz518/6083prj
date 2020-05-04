<?php
namespace Src\Controller;

use Src\Repository\UserRepo;
use Slim\Exception\HttpNotFoundException;

use \Firebase\JWT\JWT;
use \Tuupola\Base62;

class UserController {
    private $userRepo = null;

    public function __construct($db, $app){
        $this->userRepo = new UserRepo($db);
        $this->setupRoute($app);
    }

    private function setupRoute($app) {
        $app->get('/user/{id}', array($this, "getUser"));
        $app->post('/user', array($this, "newUser"));
        $app->post('/user/auth', array($this, "authUser"));
    }

    public function getUser($request, $response, $args){
        $result = $this->userRepo->find($args['id']);
        if (! $result) {
            throw new HttpNotFoundException($request);
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader("Content-Type", "application/json; charset=UTF-8");
    }

    public function newUser($request, $response, $args){
        $rc = $this->userRepo->insert($request->getParsedBody());
        return $response->withStatus(201);
    }

    public function authUser($request, $response, $args) {
        $secret = getenv("JWT_SECRET");
        $uid = $request->getParsedBody()["uid"];
        $pwd = $request->getParsedBody()["pwd"];

        $payload = [
            "uid" => $uid 
        ];

        $token = JWT::encode($payload, $secret, "HS256");
        $response->getBody()->write(json_encode(["jwt" => $token]));

        return $response->withStatus(200);
    }
}