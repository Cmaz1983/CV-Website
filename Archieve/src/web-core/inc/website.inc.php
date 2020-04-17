<?php
namespace fearricepudding;

//Include the website config file.
include_once(__DIR__.'/config.inc.php');

@session_start();

class website extends websiteConfig{

    CONST VERSION = "1.2.1";

    // error log
    static public $error = "";

    /**
     * Create database connection
     * 
     * return: (multi) [boolean] false error / [obj] valid mysql object
     */
    static public function getDB(){
        @$db = new \mysqli(self::$db_settings['host'], self::$db_settings['user'], self::$db_settings['pass'], self::$db_settings['name']);
        if ($db->connect_error) {
            self::$error = "Error connecting to database, please check your database config.";
            return false;
        };
        if( gettype($db) === 'boolean' ){
            self::$error = "Failed to connect to database.";
            return false;
        };
        return $db;
    }

    /**
     * Get check and return the users ID
     * 
     * reutrn: (multi) - User ID / false on error
     */
    static public function user_id(){
        if(isset($_SESSION['uid'])){
            if(self::verifySession()){

                return $_SESSION['uid'];    
            
            }else{
                self::$error = "User session not exist";
                return false;
            };    
        }else{
            self::$error = "User sesssion not set";
        };
    }

    /** 
     * Sanitize string
     * 
     * arg: (string) - Not sanitized
     * 
     * return : (string) - sanitized
     */
    static public function sanitize($value){
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        // We can add more sanitation if needed.
        return $value;
    }

    /**
     * Check if the user is logged in.
     * 
     * return: (boolean) - Logged in returns true
     */
    static public function checkLogin(){
        if(isset($_SESSION['uid'])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Login to website
     * 
     * arg: (string) - username
     * arg: (string) - password
     * 
     * return: (boolean) success or fail. 
     */
    static public function login($u, $p){
        if(!self::checkLogin()){

            $username =	self::sanitize($u);
            
            if(self::getDB()){
                $db = self::getDB();

                $check = $db->query("SELECT * FROM users WHERE UPPER(username) LIKE '{$username}'");
                if($check){

                    if($check->num_rows > 0){
                        while($row = $check->fetch_assoc()){
                            $hash = $row['password'];
                            if(password_verify($p, $hash)){

                                $sessionCode = self::createSessionCode();
                                $uid = $row['id'];
                                $createSession = $db->query("INSERT INTO `sessions` (`user_id`, `code`) VALUES ('$uid', '$sessionCode');");
                                if($createSession){
                                    $_SESSION['session'] = $sessionCode;
                                    $_SESSION['uid'] = $row['id'];
                                    return true;
                                }else{
                                    self::$error = "Failed to create session, please try again later";
                                    return false;
                                };
                                
                                
                            }else{
                                self::$error = "passwords do not match";
                                return false;
                            };

                        };
                    }else{
                        self::$error = "User not found";
                        return false;
                    };
                    
                }else{
                    self::$error = $db->error;
                    return false;
                };
            }else{
                return false;
            }

		    return false;
        }else{
            self::$error = "Already logged in.";
            return false;
        }

    }

    /**
     * Creates the users session code
     * 
     * return: (string) - A valid user session code
     */
    static private function createSessionCode(){
        $string = substr(md5(rand()), 0, 15).'-'.substr(md5(rand()), 0, 15).'-'.substr(md5(rand()), 0, 15).'-'.substr(md5(rand()), 0, 15);
        return $string;
    }

    /** 
     * Logs out of the user session
     * 
     * return: (boolean) valid or not
     */
    static public function logout(){
        if(self::getDB()){
            $db = self::getDB();
            $removeSession = $db->query("DELETE FROM `sessions` WHERE code='{$_SESSION['session']}'");
            session_destroy();
            return true;
        }else{
            return false;
        };
    }

    /**
    * Check if the database is setup
    *
    * return: (boolean) - Check if the database is setup or not
    */
    static public function checkDatabaseSetup(){
        if(self::getDB()){
            $db = self::getDB();
            if($db){
                $check = $db->query("SHOW TABLES LIKE 'users'");
                if($check->num_rows > 0){
                    return true;
                }else{
                    self::$error = "Databases not setup";
                    return false;
                };
            }else{
                self::$error = "Couldnt connect to database, please check your website config.";
                return false;
            };
        }
    }

    /**
     * Verifys the users session on the website. 
     * 
     * return: (boolean) - Session valid or not
     */
    static public function verifySession(){
        if(self::checkLogin()){
            if(self::getDB()){
                $db = self::getDB();
                $userid = $_SESSION['uid'];
                $sessionKey = $_SESSION['session'];
                $query = $db->query("SELECT * FROM `users` WHERE id='{$userid}'");
                if($query->num_rows > 0){
                    $checkSession = $db->query("SELECT * FROM `sessions` WHERE code='{$sessionKey}'");
                    if($checkSession->num_rows > 0){
                        $openSession = $checkSession->fetch_assoc();
                        if($openSession['user_id'] == $userid){
                            return true;
                        }else{
                            self::$error = "User session invalid, logging out.";
                            self::logout();
                            return false;
                        };
                    }else{
                        self::$error = "Session expired, logging out.";
                        self::logout();
                        return false;
                    };
                }else{
                    self::$error = "User not found, forcing logout.";
                    self::logout();
                    return false;
                };
            }else{
                self::$error = "Not logged in.";
                return false;
            }
        }
    }

    /**
     * Gets the users auth level
     * 
     * return: (multi) - [Boolean] false if error / [int] level of the user
     */
    static public function getLevel(){
        if(self::checkLogin()){
            if(self::verifySession()){
                if(self::getDB()){
                    $db = self::getDB();
                    $checkLevel = $db->query("SELECT * FROM  `users` WHERE id='{$_SESSION['uid']}'");
                    $data = $checkLevel->fetch_assoc();
                    $level = $data['level'];
                    if($level){
                        return $level;
                    }else{
                        self::$error = "User no level";
                        return false;
                    };
                };
            }else{
                return false;
            };
        }else{
            self::$error = "Not logged in.";
            return false;
        };
        return false;
    }


    /**
     * Register a user to the database
     *
     * @param string $u - Username
     * @param string $e - Email
     * @param string $p - Password
     * @param string $p2 - Confirm password
     * @return boolean
     */
    static public function register($u, $e, $p, $p2){

		$username = self::sanitize($u);
		$email = self::sanitize($e);
		$password = $p;
        $password2 = $p2;
        if(self::getDB()){
            $db = self::getDB();

            if(!self::checkLogin()){

                if($password === $password2){

                    $check = $db->query("SELECT username FROM users WHERE UPPER(username) LIKE '{$username}' OR UPPER(email) LIKE '{$email}'");
                    if($check->num_rows > 0){
                        self::$error = "User already exists";
                        return false;
                    }else{
                        $hashword = password_hash($password, PASSWORD_BCRYPT, self::$password_options);
                        $code = uniqid().uniqid();
                        $insertCode = "INSERT INTO `users` (`username`, `password`, `email`, `code`) VALUES ('$username', '$hashword', '$email', '$code')";
                        $insert = $db->query($insertCode);
                        if($insert){
                            $prefs = [
                                'USER_CODE' => $code,
                                'USER_NAME' => $username
                            ];
                            $compile = self::sendEmail($email, 'confirm_account', $prefs);

                            if($compile){

                                return true;

                            }else{

                                self::$error = "Error sending email.";
                                return false;

                            };

                        }else{
                            self::$error = "MYSQL error: ". $db->error;
                            return false;
                        };
                    };
                }else{
                    self::$error = "Passwords do not match";
                    return false;
                };
            }else{
                self::$error = "Already logged in.";
                return false;
            };
		};
		return false;
	}
    
    static private function url(){
        $url = sprintf( "%s://%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['SERVER_NAME']);
        return $url;
    }


    /**
     * Updates a users password using a reset code
     *
     * @param string $newPassword - The users new password plaintext
     * @param string $userCode - The users reset code
     * @return boolean
     */
    static public function updatePassword($newPassword, $userCode){
        $newPassword = self::sanitize($newPassword);
        $userCode = self::sanitize($userCode);

        $db = self::getDB();
        if($db){
            $SQL = "SELECT `id`, `username`, `email` FROM `users` WHERE `code`='$userCode'";
            $findUser = $db->query($SQL);
            if($findUser->num_rows > 0){
                $userData = $findUser->fetch_assoc();

                $hashword = password_hash($newPassword, PASSWORD_BCRYPT, self::$password_options);
                $SQL = "UPDATE `users` SET `password`='$hashword' WHERE `id`='{$userData['id']}' AND `code`='$userCode'";
                $update = $db->query($SQL);
                if($update){
                    $emailArray = [
                        "user_name" => $userData['username']
                    ];
                    self::sendEmail($userData['email'], "password_changed", $emailArray);
                    return true;
                }else{
                    self::$error = "Failed to update password";
                    return false;
                };

            }else{
                self::$error = "User not found.";
                return false;
            }
        }
    }

    /**
     * Update a users password with the old password
     *
     * @param int $uid - Users ID
     * @param string $old - Users old password
     * @param string $new - Users new password
     * @return boolean
     */
    static public function changePassword($uid, $old, $new){
        // Get the DB
        $db = self::getDB();
        //Sanitize the inputs
        $uid = self::sanitize($uid);
        $old = self::sanitize($old);
        $new = self::sanitize($new);

            $checkUserSQL = "SELECT * FROM `users` WHERE `id`='{$uid}'";
            $checkUser = $db->query($checkUserSQL);
            if($checkUser->num_rows > 0){
                $userData = $checkUser->fetch_assoc();
                if(password_verify($old, $userData['password'])){

                    $hashword = password_hash($new, PASSWORD_BCRYPT, self::$password_options);
                    $updateSQL = "UPDATE `users` SET `password`='{$hashword}' WHERE `id`='$uid'";
                    $update = $db->query($updateSQL);
                    if($update){
                        return true;
                    }else{
                        self::$error = "Failed to update";
                        return false;
                    };
                }else{
                    self::$error = "Current password incorrect";
                    return false;
                };
            }else{
                self::$error = "User doesn't exist";
                return false;
            };
      
        
    }

    /**
     * Gets the current users username
     *
     * @return multi Users username or boolean (false on fail)
     */
    static public function getUsername(){
        if(self::checkLogin()){
            if(self::verifySession()){
                if(self::getDB()){
                    $db = self::getDB();
                    $userid = $_SESSION['uid'];
                    $checkLevel = $db->query("SELECT * FROM `users` WHERE id='$userid'");
                    $data = $checkLevel->fetch_assoc();
                    $username = $data['username'];
                    if($username){
                        return $username;
                    }else{
                        self::$error = "User not admin";
                        return false;
                    };
                }
            }else{
                return false;
            };
        }else{
            self::$error = "Not logged in.";
            return false;
        };
        return false;
    }


    /**
     * Remove a users session data and logout all users
     *
     * @param int $id - userID
     * @return boolean
     */
    static public function clearSessions($id){
        $id = self::sanitize($id);
        $db = self::getDB();
        $checkIdSql = "SELECT * FROM `users` WHERE `id`='{$id}'";
        $checkId = $db->query($checkIdSQl);

        if($checkId->num_rows > 0){
            $removeSessionSql = "DELETE FROM `sessions` WHERE `user_id`='$id'";
            $removeSessions = $db->query($removeSessionSql);
            if($removeSession){
                return true;
            }else{
                self::$error = "Failed to remove sessions";
                return false;
            };
        }else{
            self::$error = "User not found";
            return false;
        };
    }

    

        /**
     * Check if the user allows emails to be sent
     * 
     * arg: (int) - User to check
     * 
     * return: (boolean) - Allow or not
     */
    static public function allowEmail($user_id){
        $db = self::getDB();
        $getUser = $db->query("SELECT emails FROM `users` WHERE `id`='$user_id'");
        if($getUser->num_rows > 0){
            $pref = $getUser->fetch_assoc();
            if($pref == true){
                return true;
            }else{
                return false;
            };
        }else{
            self::$error = "User not found";
            return false;
        }
    }

    


    static public function genCode(){
        return uniqid(md5(date("dDms")));
    }

    static public function forgotPassword($e){

        $db = metcalfs::getDB();

       $email = metcalfs::sanitize($e);

        $checkSQL = "SELECT * FROM `users` WHERE UPPER(email) LIKE '$email'";
        $check = $db->query($checkSQL);
        
        if($check->num_rows > 0){

            $userData = $check->fetch_assoc();

            //Update the users access key
            $newKey = self::genCode();
            $newId = $userData['id'];

            $updateKey = "UPDATE `users` SET `code`='{$newKey}' WHERE `id`='$newId'";
            $update = $db->query($updateKey);

            if($update){
                //Send email
                $emailData = [
                    "user_name" => $userData['username'],
                    "user_code" => $newKey
                ];
                $email = metcalfs::sendEmail($email, "password_reset", $emailData);
                if($email){
                    return true;
                }else{
                    self::$error = "An error occured";
                    return false;
                }
            }else{
                self::$error = "An error occured";
                return false;
            };

            return true;

        }else{
            self::$error = "User not found.";
            return false;
        };

    }


    /**
     * Sends an email
     *
     * @param string $to - The email address to send to
     * @param string $template - The name of the template to use
     * @param array $token - An array of data to be replaced
     * @return boolean
     */
    static public function sendEmail($to, $template, $token){

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: <".self::$email_from.">" . "\r\n";

        if(self::getDB()){
            $db = self::getDB();

            $getTemplate = $db->query("SELECT * FROM email_templates WHERE UPPER(name) LIKE '{$template}'");
            if($getTemplate->num_rows > 0){
                $template = $getTemplate->fetch_assoc();

                $token['website_url'] = self::url();

                $pattern = '[%s]';
                foreach($token as $key=>$val){
                    $varMap[sprintf($pattern,$key)] = $val;
                }

                $content = htmlspecialchars_decode( htmlspecialchars_decode($template['content']));

                $emailContent = strtr($content, $varMap);


                if(mail($to, $template['subject'], $emailContent, $headers)){
                    return $emailContent;
                }else{
                    self::$error = error_get_last()['message'];
                    return false;
                };
            }else{
                self::$error = "Couldn't find email template: $template";
                return false;
            };
        };
        return false;
    }


    /**
     * Gets data from a given URL
     *
     * @param string $url - URL to get the data from
     * @param string $method - GET, POST, PUT, DELETE
     * @return multi
     */
    static public function getData($url, $method){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        return $response;

        curl_close($curl);
    }

    /**
     * Create API response object
     *
     * @param int $code - Response code
     * @param string $message - Message to attatch
     * @return object
     */
    static public function response($code, $message = ""){
        $codes = [
            '200' => 'ok',
            '400' => 'Bad request',
            '401' => 'Unauthorised',
            '403' => 'Forbidden',
            '405' => 'Method not aloud',
            '0'   => 'Unknown Error',
            '500' => 'Server Error'
        ];

        $response = [
            "code"          => $code,
            "code_message"  => $codes[$code],
            "message"       => $message
        ];

        return json_encode($response);
    }


    /** 
     * Setup the database for first time use
     * 
     * return: (boolean) - Create / error
     */
    public static function DBfirstTimeSetup(){

        // Get the database connection string
        if(self::getDB()){
            $db = self::getDB();

            /**
            *   Create the database table create statements.
            */
            $userTable      = "CREATE TABLE `users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(45) DEFAULT NULL, `password` varchar(255) DEFAULT NULL, `email` varchar(255) DEFAULT NULL, `code` varchar(255) DEFAULT NULL, `active` int(11) DEFAULT '0', `locked` int(11) DEFAULT '0', `last_seen` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, `key` varchar(255) DEFAULT NULL, `level` int(11) DEFAULT '0', PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;";
            $userDetails    = "CREATE TABLE `user_details` ( `id` INT NOT NULL AUTO_INCREMENT,   `user_id` INT NULL,   `first_name` VARCHAR(255) NULL,   `last_name` VARCHAR(255) NULL,   `dob` VARCHAR(45) NULL,   PRIMARY KEY (`id`)); ";
            $emailTemplates = "CREATE TABLE `email_templates` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(45) DEFAULT NULL, `content` longtext, `subject` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;";
            $userSessions   = "CREATE TABLE `sessions` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) DEFAULT NULL, `code` varchar(255) DEFAULT NULL, `date` varchar(255) DEFAULT NULL, `info` mediumtext, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;";
            /**
            *   Create the tables in the databse
            */
            $createUserTable = $db->query($userTable);
            $createUserDetailsTable = $db->query($userDetails);
            $createEmailTemplates = $db->query($emailTemplates);
            $createSessions = $db->query($userSessions);

            if($createUserTable){
                if($createUserDetailsTable){
                    if($emailTemplates){
                        if($createSessions){
                            return true;
                        }
                    }else{
                        return false;
                    };
                }else{
                    return false;
                };
            }else{
                return false;
            };

            // Why are you here? GO HOME!
            return false;


        };
    } 


} // END OF CLASSs


/**
 * Create simple pages from an array of data
 */
class pager{

    const VERSION = '1.1.2';

    // array of data
    public $data;

    // Number of items per page (int)
    public $pp = 5;

    //Current page
    public $page;

    //Track errors
    public $error;

    // Nav button limit
    public $NAV_BUTTON_LIMIT = 9;
    
    /**
     * Will put the data into the object
     *
     * @param string $passedData - Array of items
     * @param integer $page - Requested page
     */
    public function __construct($passedData = false, $page = 0){
        if($passedData){
        	if(is_array($passedData)){
            	$this->data = $passedData;
            }else{
            	try{
            		$data = json_decode($passedData);
            		$this->data = $data;
            	}catch(Exception $e){
            		$this->error = "Data not valid array / cannot parse";
            	};
            };
        };
        $this->page = $page;
        
    }

    /**
     * Get the version number
     *
     * @return string
     */
    public function version(){
        return VERSION;
    }
     
    /**
     * Get the setup page
     *
     * @return array
     */
    public function getPage(){
        if(!empty($this->data) && is_object($this->data) || is_array($this->data)){

            // Get the total ammount of item in the array
            $total = count($this->data);
            // Get the expected ammount of pages
            $pages = ceil($total/$this->pp);

            // If the requrested page is larger than the total pages
            if($this->page > $pages){
                // Get the number for the last page of the array
                $remainder = $total%$this->pp;
                // If the remainder is a int not a float
                if($remainder == 0){
                    // The ammount is the number for the full page
                    $offset = $total-$this->pp;
                }else{
                    // The number is not a full page
                    $offset = $total-$remainder;
                };
            }else{
                if($this->page > 1 && $this->page > 0){
                    $offset = ($this->page-1)*$this->pp;
                }else{
                    $offset = 0;
                };
            };
            //Splice the requested page
            $data = array_slice($this->data, $offset, $this->pp);

            return $data;
        }else{
            return false;
        }
    }

    /**
     * Build the link for the page
     *
     * @param int $nextPage - Page for link
     * @return string
     */
    private function linker($nextPage){
        $currentQuery = $_GET;
        $currentQuery['page'] = $nextPage;
        $link = $_SERVER['PHP_SELF'].'?'.http_build_query($currentQuery);
        return $link;
    }

    /**
     * Get the navigation data for the pages
     *
     * @return array
     */
    public function nav(){
        if(!empty($this->data) && is_object($this->data) || is_array($this->data)){

            // Get the total number of items
            $total = count($this->data);
            // get the total number of pages
            $pages = ceil($total/$this->pp);
            
            // Create next and prev button arrays
            $next = array();
            $prev = array();

            // Get the next page number
            $nextPage = ($this->page+1);
            if($this->page >= $pages){
                $next['active'] = false;
            }else{
                if($pages == 1 && $this->page < 1){
                    $next['active'] = false;
                }else{
                    $next['active'] = true;
                    if($this->page <= 1){
                        $next['page'] = 2;
                        $next['link'] = $this->linker('2');
                    }else{   
                        $next['page'] = $nextPage;
                        $next['link'] = $this->linker($nextPage);
                    };
                }
            }

            // Ge the previous page number
            $prevPage = ($this->page-1);
            if($this->page <= 1){
                $prev['active'] = false;
            }else{
                if($pages == 1 && $this->page > 1){
                        $prev['active'] = false;
                }else{
                    $prev['active'] = true;
                    if($prevPage > $pages){
                        $prev['page'] = $pages;
                        $prev['link'] = $this->linker($pages-1);
                    }else{
                        
                        $prev['page'] = $prevPage;
                        $prev['link'] = $this->linker($prevPage);
                    }
                };
            }

            // Create the navigation array
            $nav = [
                'buttons' => array(),
                'next'    => $next,
                'prev'    => $prev,
                'pages'   => $pages
            ];

        

            // Loop through and create each button item
            for($i = 0; $i < $nav['pages']; $i++){
                
                $current = $i+1;

                if($current == $this->page){
                    $active = true;
                }else{

                    if($this->page == 0 && $current == 1){
                        $active = true;
                    }else{

                        if($this->page > $total && $current == $pages){

                            $active = true;

                        }else if($this->page < 0 && $current == 1){
                            $active = true;
                        }else{
                            $active = false;
                        };

                    };


                };
                
                $nav['buttons'][] = [
                    'page'   => ($i+1),
                    'link'   => $this->linker(($i+1)),
                    'active' => $active
                ];

            }

            $amm = $this->NAV_BUTTON_LIMIT; 
            $midd = ceil($amm/2);
            $start = $this->page - $midd;
            $temp = $nav['pages'] - $midd;
            $over = $this->page > $temp;
            $ammountOver = $nav['pages'] - $this->page;
            $offsetCorrct = $midd - $ammountOver;

            if($over){
                $start = $start - $offsetCorrct;
            }
            if($start < 0){
                $start = 0;
            };
            if($start > $midd){
                $start = $start+1;
            }
            $chopped = array_slice($nav['buttons'], $start, $amm);

            $nav['buttons'] = $chopped;



            // Return the navigation array
            return $nav;
        }else{
            return false;
        }
    }

}