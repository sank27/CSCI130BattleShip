<?php
include_once ('db.php');
ob_start();
session_start();
define('USER_TABLE', '`users`');

class User {
    public static function Login($login, $password){
        $response = new stdClass();

        if (empty($login)){
            $response->status = 400;
            $response->data = "Empty login";
            return $response;
        }

        if (empty($password)){
            $response->status = 400;
            $response->data = "Empty password";
            return $response;
        }

        //get the user from the db
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = "Problem with database";
            return $response;
        }

        try {
            //make a call to the db, to get the user
            $query = "SELECT * FROM " . USER_TABLE . " WHERE username=?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            //make sure the data exists
            if (empty($row['username'])) {
                $response->status = 400;
                $response->data = "Invalid login";
                return $response;
            }

            //the data exists check the password
            $hashedPassword = User::simpleEncryption($password);
            $dbPassword = $row['hashedPassword'];
            if ($hashedPassword != $dbPassword) {
                $response->status = 400;
                $response->data = "Invalid password";
                return $response;
            }

            //the passwords match, let's login the user
            $_SESSION['valid'] = true;
            $_SESSION['user'] = $row['username'];
            $_SESSION['userid'] = $row['id'];

            $newUser = new stdClass();
            $newUser->userId = $row['id'];
            $newUser->userName = $row['username'];

            $response->status = 200;
            $response->data = $newUser;
            $response->message = "Successfully logged in";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function Logout(){
        $response = new stdClass();
        $_SESSION['valid'] = false;
        $_SESSION['user'] = '';
        $_SESSION['userid'] = '';
        $response->status = 200;
        $response->data = '';
        return $response;
    }

    public static function Register($login, $password) {
        $response = new stdClass();

        if (empty($login)){
            $response->status = 400;
            $response->data = "Empty login";
            return $response;
        }

        if (empty($password)){
            $response->status = 400;
            $response->data = "Empty password";
            return $response;
        }

        //get the user from the db
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = "Problem with database";
            return $response;
        }
        try {

            //make a call to the db, to get the user
            $query = "SELECT * FROM " . USER_TABLE . " WHERE username = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            //does the user exist already?
            if (!empty($row['username'])) {
                $response->status = 400;
                $response->data = "User exists already";
                return $response;
            }

            //hash the password and save
            $hashedPassword = User::simpleEncryption($password);
            $stmt = $db->prepare("INSERT INTO " . USER_TABLE . " (`username`, `hashedPassword`,`created`) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $login, $hashedPassword);
            $stmt->execute();

            $response->status = 200;
            $response->data = "Successfully registered";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function simpleEncryption( $string, $action = 'e' ) {
        // you may change these values to your own
        $secret_key = 'kammysannicolas';
        $secret_iv = 'mybattleshipiv';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

        return $output;
    }
}
?>