<?php
namespace kajimachi\common;

use \PDO;
use \Exception;

class Transaction
{
    private $dbh;

    function __construct($db_handler)
    {
        $this->dbh = $db_handler;
        $this->dbh->beginTransaction();
    }

    private function binds($stmt, $args)
    {
        if(isset($args))
        {
            for($i = 0; $i < count($args); $i++)
            {
                if(gettype($args[$i]) == "integer")
                {
                    $stmt->bindParam($i + 1, $args[$i], PDO::PARAM_INT);
                }
                else
                {
                    $stmt->bindParam($i + 1, $args[$i], PDO::PARAM_STR);
                }
            }
        }
    }

    function do($cmd, ...$args)
    {
        try
        {
            $stmt = $this->dbh->prepare($cmd);
            $this->binds($stmt, $args);
            $stmt->execute();
        }
        catch (PDOException $e)
        {
            error_log($e->getMessage());
            exit;
        }
    }

    function get($cmd, ...$args)
    {
        try
        {
            $stmt = $this->dbh->prepare($cmd);
            $this->binds($stmt, $args);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
            error_log($e->getMessage());
            exit;
        }
    }

    function close()
    {
        $this->dbh->commit();
    }
}
