<?php
 
require_once __DIR__.'/../src/web-core/inc/website.inc.php';

use fearricepudding\website as website;
use fearricepudding\pager as pager;

class blog extends website{
    public static $name = "Colette Mazzola's Blog";
    public static $contactEmail = "jordanrandles@googlemail.com";


    public static function isAdmin(){
        return true;
    }
    public static function get_extension($file) {
        $extension = end(explode(".", $file));
        return $extension ? $extension : false;
    }
    public static function truncate($string,$length=100,$append="&hellip;") {
        $string = trim($string);

        if(strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 2);
            $string = $string[0] . $append;
        }

        return $string;
    }
}