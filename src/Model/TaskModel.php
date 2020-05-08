<?php

namespace Src\Model;

use InvalidArgumentException;
use Src\Repository\TaskRepo;

class TaskModel
{
    private $taskRepo = null;

    public function __construct($db)
    {
        $this->taskRepo = new TaskRepo($db);
    }

    public function find($id)
    {
        return $this->taskRepo->find($id);
    }

    public function findAll($query)
    {
        if (array_key_exists("pid", $query) && count($query) == 1) {
            return $this->taskRepo->findAllByPid($query["pid"]);
        } else if (array_key_exists("title", $query) && count($query) == 1) {
            return $this->taskRepo->findAllByTitle($query["title"]);
        } else {
            return [];
        }
    }

    public function create($uid, $t)
    {
        if (array_key_exists("reporter", $t)
            && $uid == $t["reporter"]
        ) {
            return $this->taskRepo->insert($t);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function update($tid, $t)
    {
        return $this->taskRepo->update($tid, $t);
    }

    public function patch($tid, $t)
    {
        if (count($t) == 1 && array_key_exists(TaskRepo::ASSIGNEES, $t)) {
            $this->taskRepo->updateAssignees($tid, $t[TaskRepo::ASSIGNEES]);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function isAssigned($uid, $tid){
        $assignees = $this->taskRepo->getAssignees($tid);
        foreach($assignees as &$a){
            if($uid == $a["uid"]){
                return true;
            }
        }
        return false;
    }
}
