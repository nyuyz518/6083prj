<?php

namespace Src\Repository;

class UserRepo
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "select uid, uname, email, display_name, created_ts from users";

        $statement = $this->db->prepare($statement);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAllByDName($dname)
    {
        $statement = "select uid, uname, email, display_name, created_ts from users 
                        where
                        MATCH(display_name) against (? IN NATURAL LANGUAGE MODE) ";

        $statement = $this->db->prepare($statement);
        $statement->execute(array($dname));
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find($id)
    {

        $statement = "select * from users where uid = ?";

        $statement = $this->db->prepare($statement);
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) != 1) {
            return null;
        } else {
            return $result[0];
        }
    }

    public function findByName($uname)
    {

        $statement = "select uid, uname, passwd from users where uname = ?";

        $statement = $this->db->prepare($statement);
        $statement->execute(array($uname));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) != 1) {
            return null;
        } else {
            return $result[0];
        }
    }

    public function insert(array $input)
    {
        $statement
            = "insert into users (uname, passwd, email, display_name, created_ts) values
            (:uname, :passwd, :email, :display_name, :created_ts) ";

        $statement = $this->db->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }

    public function update($id, array $input)
    {
        $statement
            = "update users 
                set uname = :uname, 
                    passwd = :passwd, 
                    email = :email, 
                    display_name = :display_name, 
                where uid = :uid ";

        $input['uid'] = $id;
        $statement = $this->db->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }
}
