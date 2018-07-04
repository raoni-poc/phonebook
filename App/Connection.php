<?php

namespace App;

class Connection
{
    public static function connect($host,$dbname,$user,$pass)
    {
        try{
            return new \PDO(
                "mysql:host={$host};dbname={$dbname}",
                $user,
                $pass
            );
        }catch(\PDOException $e){
            echo "Error! Message:".$e->getMessage()." Code:".$e->getCode();
            exit;
        }
    }
}