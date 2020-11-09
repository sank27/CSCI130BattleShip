<?php
include_once ('db.php');
define('PULSE_TABLE', '`pulse`');

class Pulse {
    public static function HeartBeat(){
        //get the user
        //remove records from pulse table
        //add record from pulse table

        $user = !empty($_SESSION['user']) ? $_SESSION['user'] : '';
        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';
        Pulse::Clear();//remove all pulses
        if (!empty($user) && !empty($userId)){
            $db = Database::getConnection();
            if ($db->connect_error){
                //add some logging here
                return;
            }

            //add pulse
            //remove all pulses
            $query = "INSERT INTO " . PULSE_TABLE . " (`user_id`, `player`,`created`) VALUES (?, ?, NOW())";
            $stmt = $db->prepare($query);
            $stmt->bind_param("is", $userId, $user);
            $stmt->execute();
        }
    }

    public static function Clear(){
        //run this on login/login to clear pulses
        $user = !empty($_SESSION['user']) ? $_SESSION['user'] : '';
        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';
        if (!empty($user) && !empty($userId)){
            $db = Database::getConnection();
            if ($db->connect_error){
                //add some logging here
                return;
            }

            //remove all pulses
            $query = "DELETE FROM " . PULSE_TABLE . " WHERE `user_id`=?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }
    }
}
?>