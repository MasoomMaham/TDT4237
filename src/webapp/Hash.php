<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{

    //static $salt = "1234";

    public function __construct()
    {
    }

    public static function make($plaintext)
    {
        //return hash('sha1', $plaintext . Hash::$salt);
        $salt = "";
        for ($i=0; $i < 10; $i++) { 
            $str = str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz012345‌​6789");
            $salt .= $str[0];
        }
        $hashsalt = crypt($plaintext, '$6$' . $salt) . $salt;
        return $hashsalt;
    }

    public function check($plaintext, $hash)
    {
        //return $this->make($plaintext) === $hash;
        $salt = substr($hash, -10);
        $newhash = crypt($plaintext, '$6$' . $salt) . $salt;
        return $newhash === $hash;
    }
}