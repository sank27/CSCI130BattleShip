<?php
include_once ('db.php');
define('SHIP_TABLE', '`ships`');

class Ship {
    public static function SaveShips($gameId, $ships){
        //we just save the json to the table
        $response = new stdClass();
        if (empty($gameId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Missing Game Id";
            return $response;
        }

        //connect to the database
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        try {
            //get the current user
            $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';

            //try to insert, if it fails update
            $query = "INSERT INTO " . SHIP_TABLE . " (`game_id`,`player_id`,`ships`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `ships` = VALUES(`ships`);";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iis", $gameId, $userId, $ships);
            $stmt->execute();

            $response->status = 200;
            $response->data = '';
            $response->message = "Ships added/updatedSuccessfully successfully.";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function GetShips($gameId){
//we just save the json to the table
        $response = new stdClass();
        if (empty($gameId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Missing Game Id";
            return $response;
        }

        //connect to the database
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        try {
            //get the current user
            $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';

            $query = "SELECT ships FROM " . SHIP_TABLE . " WHERE game_id = ? AND player_id = ?;";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $gameId, $userId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($ships);

            if($stmt->num_rows() == 0) { //if we have results display them
                $response->data = "";
            }else{
                $stmt->fetch();
                $response->data = $ships;
                $stmt->free_result();
            }
            $response->status = 200;
            $response->message = "Ships successfully returned.";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function GetShipsInternal($gameId){
        //no response, just return the json
        $db = Database::getConnection();
        if ($db->connect_error){
            return '';
        }

        try {
            //get the current user
            $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';

            $returnedShips = '';
            $query = "SELECT ships FROM " . SHIP_TABLE . " WHERE game_id = ? AND player_id != ?;";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $gameId, $userId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($ships);

            if($stmt->num_rows() == 0) { //if we have results display them
                $returnedShips = '';
            }else{
                $stmt->fetch();
                $returnedShips = $ships;
                $stmt->free_result();
            }
            return $returnedShips;
        }catch(Exception $e){
           return '';
        }
    }
}

?>