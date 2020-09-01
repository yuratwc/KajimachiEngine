<?php
namespace kajimachi\common;
class Util
{
    static function headJson()
    {
        header("Content-Type: application/json; charset=utf-8");
    }
}
