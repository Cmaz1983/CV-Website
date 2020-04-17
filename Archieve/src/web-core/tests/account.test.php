<?php

include('../inc/website.inc.php');
use fearricepudding\website as website;


if(website::checkLogin()){
    echo 'User data found<br>'; 
}else{
    echo'Logging in<br>';
    if(website::login('jordan', 'test')){
        echo 'succ<br><br>';
    }else{
        echo website::$error.'<br>';
    }
};

var_dump($_SESSION);


echo'<br><br>';
echo sprintf("Verify Session: %s => %s", website::verifySession() ? 'valid':'invalid', website::$error);

echo'<br><br>';
echo sprintf("Get username: %s", website::getUsername());
echo'<br><br>';

echo sprintf("<br>Errors: %s", website::$error);