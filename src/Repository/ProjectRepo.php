<?php

namespace Src\Repository;

class ProjectRepo
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "select pid, pname, description, wfid, created_ts from projects";

        $statement = $this->db->prepare($statement);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
