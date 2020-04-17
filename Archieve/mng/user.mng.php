<?php
// Include the website core
include_once '../inc/core.inc.php';

$action = $_GET['action'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if($action === "login"){
        if(!blog::checkLogin()){
            if(isset($_POST['username']) || isset($_GET['password'])){
                if(blog::login($_POST['username'], $_POST['password'])){
                    echo blog::response(200, "Success. Redirecting...");
                    exit();
                }else{
                    echo blog::response(400, "Username or password not found. ");
                    exit();
                };
            }else{
                echo blog::response(401, "Username or Password Blank.");
                exit();
            };
        }else{
            echo blog::response(400, "User already logged in. ");
            exit();
        }
    }else if($action === "watch"){
        $id = $_POST['id'];
        if(strlen($id) > 0){
            if(blog::checkLogin()){
                $userid = $_SESSION['uid'];
                $db = blog::getDB();
                $check = $db->query("SELECT * FROM `watchlist` WHERE `user_id`='$userid' AND `property_id`='$id'");
                if($check->num_rows > 0){
                    $remove = $db->query("DELETE FROM `watchlist` WHERE `property_id`='$id' AND `user_id`='$userid'");
                    if($remove){
                        echo blog::response(200, "Property removed");
                        exit();
                    }else{
                        echo blog::response(500, "An unknown error occured");
                        exit();
                    }
                    exit();
                }else{
                    $insert = $db->query("INSERT INTO `watchlist` (`user_id`, `property_id`) VALUES ('$userid', '$id')");
                    if($insert){
                        echo blog::response(200, "Property added to watch list");
                        exit();
                    }else{
                        echo blog::response(500, "An unknown error occured");
                        exit();
                    };
                };
            }else{
                echo blog::response(401, "User not logged in.");
                exit();
            };
        }else{
            echo blog::response(400, 'Invalid ID');
            exit();
        };
    }else if($action === "callback"){

        /**
         * Callback function - 
         * add user to callback list & send email to address
         */

        $name = blog::sanitize($_POST['name']);
        $number = blog::sanitize($_POST['number']);
        $property = blog::sanitize($_POST['property']);

        $db = blog::getDB();

        $sql = "INSERT INTO `callback_list` (`name`, `number`, `property`) VALUES ('{$name}', '{$number}', '{$property}')";
        $insert = $db->query($sql);
        if($insert){

            // Send the email
            $emailValues = [
                'USER_NAME'   => $name,
                'USER_NUMBER' => $number,
                'PROPERTY_ID' => $property
            ];
            $send = blog::sendEmail(blog::$contactEmail, 'callback', $emailValues);
            if($send){
                echo blog::response(200);
                exit();
            }else{
                echo blog::response(501);
                exit();
            };
        }else{
            echo blog::response(502);
            exit();
        };

    }else if($action == "register"){
        /**
         * USER REGISTER
         */

        $username = blog::sanitize($_POST['username']);
        $email = blog::sanitize($_POST['email']);
        $password = blog::sanitize($_POST['password']);
        $confirm = blog::sanitize($_POST['confirm']);
        $flag = false;

        
        if(empty($username)){
            $flag = "Data missing";
        };
        if(empty($email)){
            $flag = "Data missing";
        };
        if(empty($password)){
            $flag = "Data missing";
        }
        if(empty($confirm)){
            $flag = "Data missing";
        };
        if($password != $confirm){
            $flag = "Passwords dont match";
        };


        if($flag){
            echo blog::response(400, $flag);
        }else{
            if(blog::register($username, $email, $password, $confirm)){
                echo blog::response(200);
            }else{
                echo blog::response(400, blog::$error);
            };
            
        }
        exit();
 
    }else if($action == "deetsUpdate"){
        
        $db = blog::getDB();
        $email = blog::sanitize($_POST['email']);
        $emailAllow = blog::sanitize($_POST['emailAllow']);
        if(isset($_POST['current'])){
            $updatePassword = true;
            $current = blog::sanitize($_POST['current']);
            $password = blog::sanitize($_POST['newPass']);
            $confirm = blog::sanitize($_POST['confirm']);
        }else{
            $updatePassword = false;
        };

        $uid = blog::user_id();

        if($emailAllow == 'true'){
            $emails = '1';
        }else{
            $emails = '0';
        }

        $updateUserSql = "UPDATE `users` SET `email`='{$email}', `emails`='{$emails}' WHERE `id`='{$uid}'";
        $update = $db->query($updateUserSql);
        if($update){
            if($updatePassword){
                if(blog::changePassword($uid, $current, $password)){
                    echo blog::response(200);
                    exit();
                }else{
                    echo blog::response(400, blog::$error);
                    exit();
                }
            }else{
                echo blog::response(200);
                exit();
            }
           
        }else{
            echo blog::response(400, "An error occured.");
            exit();
        };
        

    }else if($action == "checkUsername"){

        /**
         * check if the usernames taken
         */
        
        $username = blog::sanitize($_POST['username']);
        $db = blog::getDB();

        $check = $db->query("SELECT `username` FROM `users` WHERE UPPER(`username`) LIKE '$username'");
        if($check->num_rows > 0){
            echo blog::response(200, "false");
        }else{
            echo blog::response(200, "true");
        };
        exit();

    }else if($action == "removeRemoved"){

        $properties = json_decode(blog::getProperties());
        $locations = $properties->properties;

        $db = blog::getDB();
        $user_id = $_SESSION['uid'];
        $sql = "SELECT * FROM `watchlist` WHERE `user_id`='$user_id'";
        $watchlistGet = $db->query($sql);
        $watchlist = array();
        $shown = array();
        while($row = $watchlistGet->fetch_assoc()){
            $watchlist[] = $row['property_id'];
        };
        foreach($locations as $property){
            if (in_array($property->id, $watchlist)) {
                $shown[] = $property->id;
            };
        };
        $removed = array();
        foreach($watchlist as $property){
            if(!in_array($property, $shown)){
                $removed[] = $property;
            }
        }
        if(count($removed) > 0){

            $ids = implode("','", $removed);
            $uid = blog::user_id();
            $SQL = "DELETE FROM `watchlist` WHERE `user_id`='{$uid}' AND `property_id` IN ('{$ids}')";
            
            $go = $db->query($SQL);
            if($go){
                echo blog::response(200);
                exit();
            }else{
                echo blog::response(500);
                exit();
            }
        };


    }else if($action == "forgot"){
        /** User forgot password */

        $email = blog::sanitize($_POST['email']);

        if(blog::forgotPassword($email)){
            echo blog::response(200, "Reset email sent!");
        }else{
            echo blog::response(400, blog::$error);
        }
        exit();


        
    }else if($action == "reset"){
        
        $code = blog::sanitize($_GET['code']);
        $password = blog::sanitize($_POST['password']);
        $confirm = blog::sanitize($_POST['confirm']);        
    
        if($password === $confirm){
            if(blog::updatePassword($password, $code)){
                echo blog::response(200);
                exit();
            }else{
                echo blog::response(400, "There was a problem updating the password");
                exit();
            }
        }else{
            echo blog::response(400, "Passwords don't match");
            exit();
        };

    }else{
        echo blog::response(400, "Unknown Request");
        exit();
    };

}else if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if($action === "data"){
        if(blog::checkLogin()){
            echo blog::response(000, "Yes");
        }else{
            echo blog::response(401, "User not logged in.");
            exit();
        }
    }else if($action === "logout"){
        if(blog::checkLogin()){
            if(blog::logout()){
                echo blog::response(200, "Logged out.");
            }else{
                echo blog::response(500);
            }
        };
    }else if($action === "globalLogout"){
        
        blog::clearSessions(blog::user_id());
        blog::verifySession();
        header('location: /');
        exit();

    }else{
        echo blog::response(400, "Unknown Request");
    };
 
}else{
    
    echo blog::response(405);
    exit();

};