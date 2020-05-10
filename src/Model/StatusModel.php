<?php

namespace Src\Model;

use Src\Repository\StatusRepo;

class StatusModel
{
    private $statusRepo = null;

    public function __construct(StatusRepo $statusRepo)
    {
        $this->statusRepo = $statusRepo;
    }

    public function newStatus($sname)
    {
        $this->statusRepo->insert($sname);
    }

    public function getStatus($sid)
    {
        return $this->statusRepo->find($sid);
    }

    public function listAllStatus()
    {
        return $this->statusRepo->list();
    }
}
