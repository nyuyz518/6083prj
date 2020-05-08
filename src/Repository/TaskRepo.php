<?php

namespace Src\Repository;

class TaskRepo
{
    private $db = null;

    public const ASSIGNEES = "assignees";

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

    public function find($tid)
    {

        $statement = "select * from tasks where tid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($tid));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) != 1) {
            return null;
        } else {
            $t = $result[0];
            $t[TaskRepo::ASSIGNEES] = $this->getAssignees($tid);
            return $t;
        }
    }

    public function insert($t)
    {
        try{
            $this->db->beginTransaction();
            $tid = $this->insertTask($t);
            foreach($t[TaskRepo::ASSIGNEES] as &$a){
                $this->insertAssignee($tid, $a);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($tid, array $t)
    {
        try{
            $this->db->beginTransaction();
            $this->updateTask($tid, $t);
            $this->deleteAssignees($tid);
            foreach($t[TaskRepo::ASSIGNEES] as &$a){
                $this->insertAssignee($tid, $a);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateAssignees($tid, $assignees)
    {
        try{
            $this->db->beginTransaction();
            $this->deleteAssignees($tid);
            foreach($assignees as &$a){
                $this->insertAssignee($tid, $a);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function insertTask($t)
    {
        $statement = "insert into tasks (ttype, pid, parent_tid, reporter, title, description, wfid, status, created_ts) values 
                (:ttype, :pid, :parent_tid, :reporter, :title, :description, :wfid, :status, :created_ts) ";

        $statement = $this->db->prepare($statement);
        $statement->execute([
            "ttype" => $t["ttype"],
            "pid" => $t["pid"],
            "parent_tid" => $t["parent_tid"],
            "reporter" => $t["reporter"], 
            "title" => $t["title"],
            "description" => $t["description"],
            "wfid" => $t["wfid"],
            "status" => $t["status"],
            "created_ts" => $t["created_ts"],
        ]);

        $statement = "select LAST_INSERT_ID() tid";
        $statement = $this->db->prepare($statement);
        $statement->execute();
        $val = $statement->fetch();
        return $val["tid"];
    }

    private function updateTask($tid, $t){
        $statement = "update tasks set 
                        ttype = :ttype, 
                        pid = :pid, 
                        parent_tid = :parent_tid, 
                        reporter = :reporter,
                        title = :title, 
                        description = :description, 
                        wfid = :wfid, 
                        status = :status,
                        created_ts = :created_ts
                    where tid = :tid";

        $statement = $this->db->prepare($statement);
        $statement->execute([
            "ttype" => $t["ttype"],
            "pid" => $t["pid"],
            "parent_tid" => $t["parent_tid"],
            "reporter" => $t["reporter"], 
            "title" => $t["title"],
            "description" => $t["description"],
            "wfid" => $t["wfid"],
            "status" => $t["status"],
            "created_ts" => $t["created_ts"],
            "tid" => $tid
        ]);
        return $statement->rowCount();
    }

    public function getAssignees($tid)
    {
        $statement = "select uid, assigned_ts from assignment where tid = ?";

        $statement = $this->db->prepare($statement);
        $statement->execute(array($tid));
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function insertAssignee($tid, $assignee){
        $statement = "insert into assignment (tid, uid, assigned_ts) values 
                (:tid, :uid, :assigned_ts)" ;

        $statement = $this->db->prepare($statement);
        $args = [
            "tid" => $tid,
            "uid" => $assignee["uid"],
            "assigned_ts" => $assignee["assigned_ts"]
        ];
        $statement->execute($args);
        return $statement->rowCount();
    }

    private function deleteAssignees($tid) {
        $statement = "delete from assignment where tid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($tid));
        return $statement->rowCount();
    }
}
