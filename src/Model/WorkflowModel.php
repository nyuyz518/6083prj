<?php

namespace Src\Model;

use Src\Repository\WorkflowRepo;

class WorkflowModel{
    private $wfRepo = null;
    public function __construct(WorkflowRepo $wfRepo)
    {
        $this->wfRepo = $wfRepo;
    }

    public function findWF($wfid) {
        return $this->wfRepo->find($wfid);
    }

    public function listWFs(){
        return $this->wfRepo->findAll();
    }

    public function newWF($wf){
        $this->wfRepo->insert($wf);
    }

    public function updateWF($wfid, $wf){
        $this->wfRepo->update($wfid, $wf);
    }

    public function patchWFName($wfid, $wf){
        $this->wfRepo->updateWFName($wfid, $wf["wfname"]);
    }
}