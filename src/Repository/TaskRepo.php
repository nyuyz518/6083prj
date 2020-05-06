<?php

namespace Src\Repository;

class TaskRepo
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAllByPid($pid)
    {
        $statement = "select * from tasks where pid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($pid));
        return $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAllByTitle($title)
    {
        $statement = "select * from tasks where MATCH(title) against (? IN NATURAL LANGUAGE MODE)";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($title));
        return $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find($id)
    {

        $statement = "select * from tasks where tid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) != 1) {
            return null;
        } else {
            return $result[0];
        }
    }

    public function insert(array $input)
    {
    }

    public function update($id, array $input)
    {
    }
}
