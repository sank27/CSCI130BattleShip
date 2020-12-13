<?php
include_once ('db.php');
define('ATTACK_TABLE', '`attacks`');

class SingleAttack {
    public $attack;
    public $hit;

    public function __construct($attack, $hit){
        $this->attack = $attack;
        $this->hit = $hit;
    }
}

class Attack {
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

            //get the ships and see if we have a match

            $query = "SELECT `id`, `ships` FROM " . SHIP_TABLE . " WHERE game_id = ? AND player_id != ?;";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $gameId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $rawShips = $row['ships'];
            //parse the json
            $rowId = $row['id'];
            $ships = json_decode($rawShips, false);

            $cleanAttack = str_replace('-','|', $attack);

            $successfulHit = false;
            $sankShip = false;
            $shipName = '';
            $sunkships = 0;

            $sunkenShips = array();
            //get all the sunken ships prior to attack
            foreach($ships as $singleShip){
                if ($singleShip->sunk){
                    array_push($sunkenShips, $singleShip->name);
                }
            }


            foreach($ships as $singleShip){
                foreach($singleShip->slots as $singleslot){
                    if ($singleslot == $cleanAttack){
                        $successfulHit = true;
                        array_push($singleShip->hits, $singleslot);
                    }
                }
                //if we sank we a new  ship
                sort($singleShip->slots);
                sort($singleShip->hits);
                if ($singleShip->slots == $singleShip->hits && !in_array($singleShip->name, $sunkenShips)){
                    $shipName = $singleShip->name;
                    $sankShip = true;
                    $singleShip->sunk = true;
                }
                //determine if every ship has been sunk
                $sunkships += $singleShip->sunk ? 1 : 0;
            }

            //craft message
            $message = "You successfully attacked position: " . $cleanAttack;
            if ($successfulHit){
                $message .= "<br><span class='hit'>You successfully hit a ship!</span>";
            }else{
                $message .= "<br><span class='missed'>You missed...<span>";
            }
            if ($sankShip){
                $message .= "<br><span class='sunk-ship'>You sank the enemy: " . $shipName . "</span>";
            }

            if (count($ships) == $sunkships){
                $message .= "<br> You won the game!";
                //the game has ended, stop here and end the game with this person being the winner
                Game::EndGame($gameId, $userId);
            }

            GameLog::WriteToGameLog($gameId, $message);

            //insert attack, need to determine successful hit first
            //insert the attack
            $query = "INSERT INTO " . ATTACK_TABLE . " (`game_id`, `player_id`, `attack`, `hit`,`created`) VALUES (?, ? , ?, ?, NOW())";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iisi", $gameId, $userId, $attack, $successfulHit);
            $stmt->execute();

            Game::SwitchTurns($gameId);

            //save ships only if successfully hit
            if ($successfulHit) {
                $query = "UPDATE " . SHIP_TABLE . " SET ships = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $cleanShips = json_encode($ships);
                $stmt->bind_param("si", $cleanShips, $rowId);
                $stmt->execute();
            }

            $hitResponse = new stdClass();
            $hitResponse->success = $successfulHit;
            $hitResponse->refreshships = $successfulHit;
            $hitResponse->sankship = $sankShip;

            $response->data = $hitResponse;
            $response->message = $successfulHit ? "Successfully hit ship" : "Successfully attacked";

            return $response;
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }

    public static function GetAttacks($gameId){
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

        try{
            $attacks = array();
            $response->status = 200;
            //insert the attack
            $query = "SELECT `attack`,`hit` FROM " . ATTACK_TABLE . " WHERE `game_id` = ? and `player_id` = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $gameId, $userId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($attack, $hit);

            while ($stmt->fetch()) {
                //write any records to the array of objects
                $singleAttack = new SingleAttack($attack, $hit);
                //let's get our distinct records
                array_push($attacks,$singleAttack);
            }
            $stmt->free_result();
            $response->data = $attacks;
            $response->message = "Successfully fetch players";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }
}

?>