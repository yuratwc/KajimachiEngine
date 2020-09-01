<?php
namespace kajimachi\pages;

use kajimachi\common\Auth;
use kajimachi\common\Util;

class AuthController
{
    public function login()
    {
        $raw_json = file_get_contents("php://input");
        $json = json_decode($raw_json, true);

        if(!isset($json['username']) || !isset($json['password']) )
            exit;
        
        Util::headJson();
        if(Auth::login($json['username'], $json['password']))
        {
            echo json_encode(array("status"=>1));
            exit;
        }
        
        echo json_encode(array("status"=>0));
    }

    public function logout()
    {
        Auth::logout();
    }
}
