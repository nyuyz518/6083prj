<?php

namespace Src\Repository;

use Src\System\DatabaseConnector;

class StatusRepo
{
    private $db = null;
    public function __construct(DatabaseConnector $databaseConnector)
    {
        $this->db = $databaseConnector->getConnection();
    }

    public function insert($s)
    {
        $statement = "insert into status (sname) values (:sname)";
        $statement = $this->db->prepare($statement);
        $statement->execute([
            "sname" => $s["sname"]
        ]);
        return $statement->rowCount();
    }

    public function find($sid)
    {
        $statement = "select sid, sname from status where sid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($sid));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) != 1) {
            return null;
        } else {
            return $result[0];
        }
    }

    public function list()
    {
        $statement = "select sid, sname from status";
        $statement = $this->db->prepare($statement);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
