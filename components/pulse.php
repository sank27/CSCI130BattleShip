<?php
include_once ('db.php');
define('PULSE_TABLE', '`pulse`');

class PulsePlayer {
    public $id;
    public $player;

    public function __construct($userid, $username){
        $this->id = $userid;
        $this->player = $username;
    }
}

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

    public static function GetPlayers() {
        $response = new stdClass();
        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';

        $db = Database::getConnection();
        if ($db->connect_error){
            //add some logging here
            return;
        }

        try {
            $response->status = 200;

            $players = array();

            $query = "SELECT user_id, player FROM " . PULSE_TABLE . " WHERE user_id <> ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id, $player);

            if($stmt->num_rows() == 0) { //if we have results display them
                $response->data = $players;
            }else{
                $includedGroups = array();
                while ($stmt->fetch()) {
                    //write any records to the array of objects
                    $singlePlayer = new PulsePlayer($user_id, $player);
                    //let's get our distinct records
                    array_push($players,$singlePlayer);
                }
                $stmt->free_result();
                $response->data = $players;
            }
        }catch(Exception $e){
            $response->status = 422;
            $response->data = $e->getMessage();
        }
        return $response;
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