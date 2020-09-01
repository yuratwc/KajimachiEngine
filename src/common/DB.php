<?php
namespace kajimachi\common;

use \PDO;

class DB
{
    public static function open()
    {
        try
        {
            $dbptr = new PDO(DATABASE_SOURCE);
            $dbptr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbptr->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) 
        {
            exit;
        }

        return $dbptr;
    }

    public static function begin()
    {
        $dbh = DB::open();
        return new Transaction($dbh);
    }
    
    public static function fetch($cmd)
    {
        try
        {
            $db = DB::open();
            $stmt = $db->query($cmd);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
            exit;
        }
    }


    public static function query($cmd)
    {
        try
        {
            $db = DB::open();
            $db->query($cmd);
        }
        catch (PDOException $e)
        {
            exit;
        }
    }

    public static function queryPrepare($cmd, ...$args)
    {
        try
        {
            $db = DB::open();
            $stmt = $db->prepare($cmd);

            for($i = 0; $i < count($args); $i++)
            {
                if(gettype($args[$i]) == "integer")
                {
                    $int_param = intval($args[$i]);
                    $stmt->bindParam($i + 1, $int_param, PDO::PARAM_INT);
                }
                else
                {
                    $stmt->bindParam($i + 1, $args[$i], PDO::PARAM_STR);
                }
            }
            $stmt->execute();
        }
        catch (PDOException $e)
        {
            exit;
        }
    }

    public static function fetchPrepare($cmd, ...$args)
    {
        try
        {
            $db = DB::open();
            $stmt = $db->prepare($cmd);
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
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
            exit;
        }
    }
}

