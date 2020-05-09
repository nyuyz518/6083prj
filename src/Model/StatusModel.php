<?php

namespace Src\Model;

use Src\Repository\StatusRepo;

class StatusModel
{
    private $statusRepo = null;

    public function __construct($db)
    {
        $this->statusRepo = new StatusRepo($db);
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
