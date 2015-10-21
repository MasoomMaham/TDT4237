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

    function hash_equals($str1, $str2) {
        if(strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
            return !$ret;
        }
    }


    public function check($plaintext, $hash)
    {
        //return $this->make($plaintext) === $hash;
        $salt = substr($hash, -10);
        $newhash = crypt($plaintext, '$6$' . $salt) . $salt;
        return ($this->hash_equals($newhash, $hash));
    }
}