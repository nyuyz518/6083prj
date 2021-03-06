<?php

namespace Src\Model;

use InvalidArgumentException;
use Src\Repository\ProjectRepo;

class ProjectModel
{
    private $projectRepo = null;

    public function __construct(ProjectRepo $projectRepo)
    {
        $this->projectRepo = $projectRepo;
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
        return $this->projectRepo->find($id);
    }

    public function create($uid, $p)
    {
        if(array_key_exists(ProjectRepo::OWNERS, $p) 
            && count($p[ProjectRepo::OWNERS]) == 1
            && $uid == $p[ProjectRepo::OWNERS][0]["uid"]){
                return $this->projectRepo->insert($p);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function update($pid, $p)
    {
        $this->projectRepo->update($pid, $p);
    }

    public function patch($pid, $p)
    {
        if(count($p) == 1 && array_key_exists(ProjectRepo::OWNERS, $p)){
            $this->projectRepo->updateOwners($pid, $p[ProjectRepo::OWNERS]);
        } else {
            throw new InvalidArgumentException();
        }
    }
}
