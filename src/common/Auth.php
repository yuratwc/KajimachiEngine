<?php
namespace kajimachi\common;

class Auth
{
    public static function login($username, $password)
    {
        if(Auth::check())
            return false;
        
        if($username !== LOGIN_ID)
            return false;
        
        if(!password_verify($password, LOGIN_PASSWORD_HASH))
            return false;
        
        session_regenerate_id(true);
        $_SESSION['lu'] = $username;
        return true;
    }

    public static function logout()
    {
        Auth::check();
        $_SESSION = array();
        @session_destroy();
    }

    public static function check()
    {
        session_start();

        if(DEBUG_MODE)
            return true;
        
        return isset($_SESSION['lu']);
    }
}
