<?php
include_once ('db.php');
define('ATTACK_TABLE', '`attacks`');

class Attack {
    //we need the player id and game id
    //write the attack to the table

    //get the ships for the game for the opponent

    //determine if the attack hit a ship

    //attack did hit a ship
    //update the ships and write back to the table
    //inform the player the attack hit something

    //attack missed the ship
    //do nothing
    public static function SaveAttack($gameId, $attack){
        $response = new stdClass();

        //connect to the database
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        //get the current user
        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';

        try {
            $response->status = 200;
            //insert the attack
            $query = "INSERT INTO " . ATTACK_TABLE . " (`game_id`, `player_id`, `attack`, `created`) VALUES (?, ? , ?, NOW())";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iis", $gameId, $userId, $attack);
            $stmt->execute();

            //get the ships and see if we have a match
            $rawShips = Ship::GetShipsInternal(`game_id`);

            //parse the json
            $ships = json_decode($rawShips, true);
            //todo

            $response->data = true;
            $response->message = "Successfully hit ship";

            // TODO: DETERMINE IF THE SHIP HAS SUNK
            return $response;
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;

    }

    public static function GetAttacks($gameId){

    }
}

?>