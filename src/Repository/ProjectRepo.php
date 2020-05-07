<?php

namespace Src\Repository;

class ProjectRepo
{
    private $db = null;

    public const OWNERS = "owners";

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

    public function find($id)
    {
        $statement = "select pid, pname, description, wfid, created_ts from projects where pid = ?";

        $statement = $this->db->prepare($statement);
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) != 1) {
            return null;
        } else {
            return $result[0];
        }
    }

    public function insert($p)
    {
        try{
            $this->db->beginTransaction();
            $pid = $this->insertProject($p);
            foreach($p[ProjectRepo::OWNERS] as &$owner){
                $this->insertOwner($pid, $owner);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($pid, $p)
    {
        try{
            $this->db->beginTransaction();
            $this->updateProject($pid, $p);
            $this->deleteOwners($pid);
            foreach($p[ProjectRepo::OWNERS] as &$owner){
                $this->insertOwner($pid, $owner);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    } 

    public function getOwners($pid)
    {
        $statement = "select uid, created_ts from ownership where pid = ?";

        $statement = $this->db->prepare($statement);
        $statement->execute(array($pid));
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateOwners($pid, $owners)
    {
        try{
            $this->db->beginTransaction();
            $this->deleteOwners($pid);
            foreach($owners as &$owner){
                $this->insertOwner($pid, $owner);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function insertProject($p)
    {
        $statement = "insert into projects (pname, description, wfid, created_ts) values 
                (:pname, :description, :wfid, :created_ts) ";

        $statement = $this->db->prepare($statement);
        $statement->execute([
            "pname" => $p["pname"],
            "description" => $p["description"],
            "wfid" => $p["wfid"],
            "created_ts" => $p["created_ts"],
        ]);

        $statement = "select LAST_INSERT_ID() pid";
        $statement = $this->db->prepare($statement);
        $statement->execute();
        $val = $statement->fetch();
        return $val["pid"];
    }

    private function updateProject($pid, $p)
    {
        $statement = "update projects set 
                        pname = :pname, 
                        description = :description, 
                        wfid = :wfid, 
                        created_ts = :created_ts
                    where pid = :pid";

        $statement = $this->db->prepare($statement);
        $statement->execute([
            "pname" => $p["pname"],
            "description" => $p["description"],
            "wfid" => $p["wfid"],
            "created_ts" => $p["created_ts"],
            "pid" => $pid
        ]);
        return $statement->rowCount();
    }

    private function insertOwner($pid, $owner){
        $statement = "insert into ownership (pid, uid, created_ts) values 
                (:pid, :uid, :created_ts)" ;

        $statement = $this->db->prepare($statement);
        $args = [
            "pid" => $pid,
            "uid" => $owner["uid"],
            "created_ts" => $owner["created_ts"]
        ];
        $statement->execute($args);
        return $statement->rowCount();
    }

    private function deleteOwners($pid) {
        $statement = "delete from ownership where pid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($pid));
        return $statement->rowCount();
    }
}
