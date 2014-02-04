<?php

namespace Ishark\Services;

class ConvertService
{
    public static function packmd5($md5)
    {
        return str_replace(array('/', '+'), array('_', '-'), substr(base64_encode(pack('H*', $md5)), 0, -2));
    }

    public static function unpackmd5($pack)
    {
        return bin2hex(base64_decode(str_replace(array('_', '-'), array('/', '+'), $pack) . '=='));
    }
} 