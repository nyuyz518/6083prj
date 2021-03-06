<?php

namespace Src\Model;

use Src\Repository\UserRepo;

class UserModel
{
    private $userRepo = null;

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function findAll($query)
    {
        if (array_key_exists("dname", $query)) {
            return $this->userRepo->findAllByDName($query["dname"]);
        } else {
            return $this->userRepo->findAll();
        }
    }

    public function find($id)
    {
        $record = $this->userRepo->find($id);
        return $record ? [
            'uid' => $record['uid'],
            'uname' => $record['uname'],
            'email' => $record['email'] ?? null,
            'display_name' => $record['display_name'] ?? null,
            'created_ts' => $record['created_ts'],
        ] : $record;
    }

    public function auth($uname, $pwd)
    {
        $hash = hash('sha256', $pwd);
        $user = $this->userRepo->findByName($uname);
        return $user != null && $user['passwd'] == $hash ? $user["uid"] : null;
    }

    private function prepareRepoUser(array $input)
    {
        return [
            'uname' => $input['uname'],
            'passwd'  => hash('sha256', $input['passwd']),
            'email' => $input['email'] ?? null,
            'display_name' => $input['display_name'] ?? null,
            'created_ts' => $input['created_ts'],
        ];
    }

    public function create(array $input)
    {
        $user = $this->userRepo->findByName($input["uname"]);
        if ($user) {
            return 0;
        }
        $dbUser = $this->prepareRepoUser($input);
        return $this->userRepo->insert($dbUser);
    }

    public function update($uid, array $input)
    {
        $dbUser = $this->prepareRepoUser($input);
        $this->userRepo->update($uid, $dbUser);
    }
}
