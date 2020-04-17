<?php
    include(__DIR__.'/../inc/website.inc.php');
    use fearricepudding\website as website;

    class test extends website{
        public $name = "Cool website!";

        public static function testing(){
            echo "This is a test!";
        }
    }


    test::testing();