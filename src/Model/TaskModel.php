<?php

namespace Src\Model;

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
}
