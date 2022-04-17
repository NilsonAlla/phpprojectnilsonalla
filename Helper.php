<?php

abstract class Helper
{
    public static function response($data = [], $status = 200, $header = 'Content-Type: application/json', $replace = '')
    {
        header($header, $replace, $status);
        return json_encode($data);
    }

    public static function echoAndExit($data) :void
    {
        echo $data;
        exit();
    }
}