<?php
include_once ('db.php');
DEFINE('GAME_LOG_TABLE', '`gamelog`');

class GameLog{
    public static function GetGameLog($gameId){
        //get my log from the system

        $response = new stdClass();
        if (empty($gameId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid Game Id";
            return $response;
        }

        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        try {
            $response->status = 200;
            $query = "SELECT `message` FROM " . GAME_LOG_TABLE . " WHERE `game_id` = ? AND `player_id` = ? ORDER BY `created` DESC";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $gameId, $userId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($message);

            $messages = array();

            while ($stmt->fetch()) {
                array_push($messages, $message);
            }

            $stmt->free_result();
            $response->data = $messages;
            $response->message = "Successfully fetched messages";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }

    public static function WriteToGameLog($gameId, $message){
        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';
        $db = Database::getConnection();
        if ($db->connect_error){
           return;
        }

        try {
            //add to the game log table
            $query = "INSERT INTO " . GAME_LOG_TABLE . " (`game_id`, `player_id`,`message`, `created`) VALUES (?, ?, ?, NOW())";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iis", $gameId, $userId, $message);
            $stmt->execute();
        }catch(Exception $e){
            //there was a problem //add some logging
        }
    }
}

?>