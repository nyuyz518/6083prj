<?php
namespace Src\Repository;

class UserRepo {
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
        
        $statement = "select * from users where uid = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if(count($result) != 1){
                return null;
            } else {
                return $result[0];
            }
        } catch (\PDOException $e) {
            exit($e->getMessage());
        } 
    }
    
    public function insert(Array $input){
        $statement 
            = "insert into users (uname, passwd, email, display_name, created_ts) values
            (:uname, :passwd, :email, :display_name, :created_ts)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'uname' => $input['uname'],
                'passwd'  => $input['passwd'],
                'email' => $input['email'] ?? null,
                'display_name' => $input['display_name'] ?? null,
                'created_ts' => $input['created_ts'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input){

    }

    public function delete($id){

    }
}