<?php
namespace Src\Repository;

class TaskRepo {
    private $db = null;

    public function __construct($db){
        $this->db = $db;
    }

    public function findAll(){
        $statement = "";
    }

    public function search(){

    }

    public function find($id){
        
        $statement = "select * from tasks where tid = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result[0];
        } catch (\PDOException $e) {
            exit($e->getMessage());
        } 
    }
    
    public function insert(Array $input){

    }

    public function update($id, Array $input){

    }

    public function delete($id){

    }

}