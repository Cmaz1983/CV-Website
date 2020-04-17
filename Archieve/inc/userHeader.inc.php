<?php
    include_once('core.inc.php');

    if(blog::checkLogin()){
?>

    <div class="header-login-logged">
      
        <a href="/user.php" class="btn btn-light">user</a>
        <button onclick="logout()" class="btn btn-outline-light">Logout</a>
    </div>

<?php
    }else{
?>

    <!-- <a href="#" class="header-login-btn">Login</a>  -->
    <button type="button" class="btn btn-outline-light" data-toggle="modal" data-target="#loginModal">
        Login
    </button>
    <!-- <button type="button" class="btn btn-light" data-toggle="modal" data-target="#registerModal">
        Register
    </button> -->

<?php
    };
?> 