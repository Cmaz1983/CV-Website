<?php

    include_once(__DIR__.'/core.inc.php');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= blog::$name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Jquery -->
    <script src="/js/jquery-3.3.1.min.js"></script>

    <!-- Bootstrap -->
    <script src="/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/bootstrap-grid.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/bootstrap-reboot.min.css">

    <!-- Slick.js -->
    <link type="text/css" rel="stylesheet" media="screen" href="/css/slick.css">
    <script src="/js/slick.min.js"></script>

    <!-- Fontawesome.com -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <!-- https://github.com/jonsuh/hamburgers -->
    <link type="text/css" rel="stylesheet" href="/css/hamburgers.css" />

    <!-- Rellax js library [https://github.com/dixonandmoe/rellax] -->
    <script src="/js/rellax.js"></script>

    <!-- Chart.js [https://www.chartjs.org] -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

    <!-- Main Quill library -->
    <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <!-- SweetAlert2 [https://sweetalert2.github.io/#download] -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

    <!-- Theme included stylesheets -->
    <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="//cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">

    <!-- Overrides -->
    <link rel="stylesheet" type="text/css" media="screen" href="/css/core.css" />
    <script src="/js/core.js"></script>

    <!-- Mobile stylesheet changes -->
    <link rel="stylesheet" type="text/css" media="screen" href="/css/breakpoints.css" />

    <!-- Google analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-135993916-1"></script>
    <script>

        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-135993916-1');

    </script>
    <!-- End of google analytics -->

</head>
<body>

    <?php
    /** Outside website container includes */
    include_once('mobileNav.inc.php');
    include_once('login.inc.php');
    // include_once('register.inc.php');
    include_once('cookieConsent.inc.php');
    ?>

    <div class="website-container" id="main">
    
        <?php
            /** INSIDE website container includes */
            include_once('header.inc.php'); 
        ?>

    

