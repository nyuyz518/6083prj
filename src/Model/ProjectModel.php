<?php

namespace Src\Model;

use Src\Repository\ProjectRepo;

class ProjectModel
{
    private $projectRepo = null;

    public function __construct($db)
    {
        $this->projectRepo = new ProjectRepo($db);
    }

    public function findAll()
    {
        return $this->projectRepo->findAll();
    }
}
