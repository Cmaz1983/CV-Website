<?php

    include(__DIR__.'/../inc/core.inc.php');

    $inputs = [
        'USER_NAME'      => $_POST['name'],
        'WEBSITE_NAME'   => blog::$name,
        'USER_EMAIL'     => $_POST['email'],
        'EMAIL_SUBJECT'  => $_POST['subject'],
        'EMAIL_MESSAGE'  => $_POST['message']
    ];

    if(blog::sendEmail(blog::$contactEmail, 'contact', $inputs)){
        echo blog::response(200, "ok");
        exit();
    }else{
        echo blog::response(500, blog::$error);
        exit();
    };