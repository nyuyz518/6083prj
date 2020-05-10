<?php

namespace Src\Repository;

use Exception;

class WorkflowRepo
{
    private $db = null;
    private const STATE_MACHINE = "state_machine";
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function find($wfid){
        $statement = "select wfid, wfname, created_ts from workflows where wfid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($wfid));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if(count($result) != 1){
            return null;
        }
        $wf = $result[0];
        $wf[WorkflowRepo::STATE_MACHINE] = $this->findSMEntries($wfid);
        return $wf;
    }

    public function findAll(){
        $statement = "select wfid, wfname, created_ts from workflows";
        $statement = $this->db->prepare($statement);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insert($wf)
    {
        try {
            $this->db->beginTransaction();
            $wfid = $this->insertWF($wf);
            foreach($wf[WorkflowRepo::STATE_MACHINE] as $sme){
                $this->insertSMEntry($wfid, $sme);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($wfid, $wf)
    {
        try {
            $this->db->beginTransaction();
            $this->updateWFName($wfid, $wf["wfname"]);
            $this->deleteSMEntries($wfid);
            foreach($wf[WorkflowRepo::STATE_MACHINE] as $sme){
                $this->insertSMEntry($wfid, $sme);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateWFName($wfid, $wfname)
    {
        $statement = "update workflows set wfname = :wfname where wfid = :wfid";
        $statement = $this->db->prepare($statement);
        $statement->execute([
            "wfname" => $wfname,
            "wfid" => $wfid
        ]);
    }

    private function findSMEntries($wfid){
        $statement = "select from_status, to_status from wf_state where wfid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($wfid));
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function insertWF($wf)
    {
        $statement = "insert into workflows (wfname, created_ts) 
                values (:wfname, :created_ts)";
        $statement = $this->db->prepare($statement);
        $statement->execute([
            "wfname" => $wf["wfname"],
            "created_ts" => $wf["created_ts"]
        ]);
        $statement = "select LAST_INSERT_ID() wfid";
        $statement = $this->db->prepare($statement);
        $statement->execute();
        $val = $statement->fetch();
        return $val["wfid"];
    }

    private function insertSMEntry($wfid, $smEntry)
    {
        $statement = "insert into wf_state (wfid, from_status, to_status) 
                    values (:wfid, :from_status, :to_status)";
        $statement = $this->db->prepare($statement);
        $statement->execute([
            "wfid" => $wfid,
            "from_status" => $smEntry["from_status"],
            "to_status" => $smEntry["to_status"]
        ]);
        return $statement->rowCount();
    }

    private function deleteSMEntries($wfid)
    {
        $statement = "delete from wf_state where wfid = ?";
        $statement = $this->db->prepare($statement);
        $statement->execute(array($wfid));
    }
}
