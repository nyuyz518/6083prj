<?php

namespace Src\Model;

use InvalidArgumentException;
use Src\Exceptions\UnAuthorizedException;
use Src\Repository\ProjectRepo;

class ProjectModel
{
    private $projectRepo = null;

    public function __construct($db)
    {
        $this->projectRepo = new ProjectRepo($db);
    }

    public function isOwner($uid, $pid)
    {
        $owners = $this->projectRepo->getOwners($pid);
        foreach($owners as &$owner){
            if($uid == $owner["uid"]){
                return true;
            }
        }
        return false;
    }

    public function findAll()
    {
        return $this->projectRepo->findAll();
    }

    public function getProject($id)
    {
        $p = $this->projectRepo->find($id);
        if($p){
            $p[ProjectRepo::OWNERS] = $this->projectRepo->getOwners($id);
            return $p;
        }
        return null;
    }

    public function create($uid, $p)
    {
        if(array_key_exists(ProjectRepo::OWNERS, $p) 
            && count($p[ProjectRepo::OWNERS]) == 1
            && $uid == $p[ProjectRepo::OWNERS][0]["uid"]){
                $this->projectRepo->insert($p);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function update($uid, $pid, $p)
    {
        $this->projectRepo->update($pid, $p);
    }

    public function patch($uid, $pid, $p)
    {
        if(count($p) == 1 && array_key_exists(ProjectRepo::OWNERS, $p)){
            $this->projectRepo->updateOwners($pid, $p[ProjectRepo::OWNERS]);
        } else {
            throw new InvalidArgumentException();
        }
    }
}
