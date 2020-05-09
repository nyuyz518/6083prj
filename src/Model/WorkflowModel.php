<?php

namespace Src\Model;

use Src\Repository\WorkflowRepo;

class WorkflowModel{
    private $wfRepo = null;
    public function __construct($db)
    {
        $this->wfRepo = new WorkflowRepo($db);
    }

    
}