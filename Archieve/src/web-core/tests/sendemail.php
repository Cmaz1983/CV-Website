<?php

include_once(__DIR__.'/../inc/website.inc.php');
use fearricepudding\website as website;

$testEmailAddress = "web@propertyvisionuk.co.uk";

$dataArray = [
    "username" => "Jordan Randles",
    "message"  => "Your post has recieved a new like!"
];

$sendEmail = website::sendEmail($testEmailAddress, "test_template", $dataArray);
if($sendEmail){
    echo "Email was sent!";
}else{
    echo website::$error;
};

