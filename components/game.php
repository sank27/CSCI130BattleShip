<?php
include_once ('db.php');
define('GAME_TABLE', '`games`');
define('PLAYER_TABLE', '`players`');

class Game {
    public static function GetCurrentGame(){
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
            //is there a current game with this player?
            $query = "SELECT g.* FROM " . GAME_TABLE . " g INNER JOIN " . PLAYER_TABLE . " p ON g.id = p.game_id WHERE finished = FALSE AND p.player_id = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $newGame = new stdClass();
            if (!empty($row['id'])) { //we found a game, let's return the information
                $newGame->id = $row['id'];
                $newGame->turn = $row['turn'];
            }else{
                $newGame->id = 0;
                $newGame->turn = 0;
            }
            $response->data = $newGame;
            $response->message = 'current game message';
            return $response;
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }

    public static function StartGame($gameId){
        $response = new stdClass();
        //make sure we have an opponent
        if (empty($gameId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid Game Id";
            return $response;
        }


        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        try {
            //get the 2 player ids
            $query = "SELECT player_id FROM " . PLAYER_TABLE . " WHERE `game_id` = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $gameId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($player_id);

            $players = array();

            if($stmt->num_rows() == 0) { //if we do not have any players something is wrong....
                throw new Exception("Missing player information for game...");
            }else{
                $includedGroups = array();
                while ($stmt->fetch()) {
                    array_push($players, $player_id);
                }
                $stmt->free_result();
            }

            //pick one of the 2 players to start with
            $starting_player = array_rand($players, 1);

            //update the game to start it and set the player
            $query = "UPDATE " . GAME_TABLE . " SET `started` = ? AND `turn` = ? WHERE `id` = ?";
            $stmt = $db->prepare($query);
            $gameStart = true;
            $stmt->bind_param("iii", $gameStart, $starting_player, $gameId);
            $stmt->execute();

            //Send a successful response
            $response->status = 200;
            $response->data = '';
            $response->message = 'Game successfully created';
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }

    public static function CheckGameStart($gameId){
        $response = new stdClass();
        //make sure we have an opponent
        if (empty($gameId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid Game Id";
            return $response;
        }

        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        try {
            //get the 2 player ids
            $query = "SELECT started FROM " . GAME_TABLE . " WHERE `id` = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $gameId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            //Send a successful response
            $response->status = 200;
            $response->data = $row['started'];
            $response->message = 'Game check successful';
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }

    public static function DoTurn($gameId){

    }

    public static function CreateGame($opponentId)
    {
        $response = new stdClass();
        //make sure we have an opponent
        if (empty($opponentId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid User Id";
            return $response;
        }

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

            //sure they are logged in
            $query = "SELECT * FROM " . PULSE_TABLE . " WHERE user_id = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $opponentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->free_result();

            if (empty($row['player'])) {
                $response->status = 400;
                $response->data = '';
                $response->message = "User is not logged in";
                return $response;
            }

            //create the game
            $query = "INSERT INTO " . GAME_TABLE . " (`turn`, `started`, `created`, `finished`) VALUES (0, FALSE , NOW(), FALSE)";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $last_id = $db->insert_id;

            //add players to the game

            //add current player
            $query = "INSERT INTO " . PLAYER_TABLE . " (`game_id`, `player_id`, `created`) VALUES (?, ? , NOW())";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $last_id, $userId);
            $stmt->execute();

            //add opponent
            $query = "INSERT INTO " . PLAYER_TABLE . " (`game_id`, `player_id`, `created`) VALUES (?, ? , NOW())";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $last_id, $opponentId);
            $stmt->execute();

            //Send a successful response
            $response->status = 200;
            $response->data = '';
            $response->message = 'Game successfully created';
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }

    public static function CleanUp(){
        $response = new stdClass();
        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        //find any open requests and mark them as declined
        try {
            $response->status = 200;
            $query = "SELECT g.* FROM " . GAME_TABLE . " g INNER JOIN " . PLAYER_TABLE . " p ON g.id = p.game_id WHERE finished = FALSE AND p.player_id = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if (!empty($row['id'])) {
                //mark the game as ended...
                //TODO: end the game for the other player
                $query = "UPDATE " . GAME_TABLE . " SET finished = TRUE WHERE id = ? LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
            }

            $response->data = '';
            $response->message = 'Game cleanup complete!';
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }
}
?>