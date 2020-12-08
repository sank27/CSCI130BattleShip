<?php
include_once ('db.php');
define('REQUEST_TABLE', '`request`');

abstract class RequestStatus {
    const INITIATED = 0;
    const APPROVED = 1;
    const DECLINE = 2;
}

class Request {
    public static function MakeRequest($opponentId){
        $response = new stdClass();
        if (empty($opponentId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid User Id";
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
            //make sure it's a valid user
            $query = "SELECT * FROM " . USER_TABLE . " WHERE id = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $opponentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->free_result();

            //make sure the user exists
            if (empty($row['username'])) {
                $response->status = 400;
                $response->data = '';
                $response->message = "User does not exist";
                return $response;
            }

            //make sure the user is logged in (not stale data);

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

            //add to the request table
            $query = "INSERT INTO " . REQUEST_TABLE . " (`requester`, `requestee`,`status`) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $initiated = RequestStatus::INITIATED;
            $stmt->bind_param("iii", $userId, $opponentId, $initiated);
            $stmt->execute();
            $last_id = $db->insert_id;

            //return success
            $response->status = 200;
            $response->data = $last_id;
            $response->message = "Request successfully made.";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function CheckRequest($requestId){
        //check on the status of the request, you can only have one request at time
        $response = new stdClass();
        if (empty($requestId)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid Request Id";
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
            $response->status = 200;
            //check is my response has been declined
            $query = "SELECT * FROM " . REQUEST_TABLE . " WHERE id = ? AND status = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $declined = RequestStatus::DECLINE;
            $stmt->bind_param("ii", $requestId, $declined);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->free_result();
            if (!empty($row['requestee'])) {
                //the request has been declined
                $response->data = 'declined';
                $response->message = "Game Request Denied.";
                return $response;
            }

            $query = "SELECT * FROM " . REQUEST_TABLE . " WHERE id = ? AND status = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $approved = RequestStatus::APPROVED;
            $stmt->bind_param("ii", $requestId, $approved);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->free_result();

            if (empty($row['requestee'])) {
                $response->data = 'pending';
                $response->message = "Request successfully made.";
            }else{
                $response->data = 'approved';
                $response->message = "Request successfully approved.";
            }
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;

    }

    public static function GetRequests(){
        $response = new stdClass();
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
            $query = "SELECT r.*, u.username FROM " . REQUEST_TABLE . " r INNER JOIN users u ON r.requestee = u.id WHERE requestee = ? AND status = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $initiated = RequestStatus::INITIATED;
            $stmt->bind_param("ii", $userId, $initiated);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->free_result();

            if (!empty($row['username'])) {
                $gameRequest = new stdClass();
                $gameRequest->id = $row['id'];
                $gameRequest->user = $row['username'];
                $response->data = $gameRequest;
                $response->message = 'Found a request!';
            }else{
                $response->data = '';
                $response->message = 'no requests found...';
            }
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }

    public static function DeclineRequest($request){
        $response = new stdClass();
        if (empty($request)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid Request Id";
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
            //add to the request table
            $query = "UPDATE " . REQUEST_TABLE . " SET `status` = ? WHERE `id` = ?";
            $stmt = $db->prepare($query);
            $declined = RequestStatus::DECLINE;
            $stmt->bind_param("ii", $declined, $request);
            $stmt->execute();
            $last_id = $db->insert_id;

            //return success
            $response->status = 200;
            $response->data = $last_id;
            $response->message = "Request successfully declined.";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function CheckExistingRequests(){
        $response = new stdClass();
        $db = Database::getConnection();
        if ($db->connect_error){
            $response->status = 404;
            $response->data = '';
            $response->message = "Problem with database";
            return $response;
        }

        $userId = !empty($_SESSION['userid']) ? $_SESSION['userid'] : '';
        try{
            $response->status = 200;
            //find any requests where I am the requestor that are still open
            $query = "SELECT `id`, `requestee`, `created` FROM " . REQUEST_TABLE . " WHERE requester = ? AND status = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $initiated = RequestStatus::INITIATED;
            $stmt->bind_param("ii", $userId, $initiated);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->free_result();

            $gameRequest = new stdClass();
            if (!empty($row['id'])) {
                $gameRequest->id = $row['id'];
                $gameRequest->requestee = $row['requestee'];
                $gameRequest->created = $row['created'];
                $response->data = $gameRequest;
                $response->message = 'Found a request!';
            }else{
                $gameRequest = new stdClass();
                $gameRequest->id = false;
                $response->data = $gameRequest;
                $response->message = 'No request found...';
            }
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function CleanUpRequest($request){
        $response = new stdClass();
        if (empty($request)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid Request Id";
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
            //add to the request table
            $query = "UPDATE " . REQUEST_TABLE . " SET `status` = ? WHERE `id` = ?";
            $stmt = $db->prepare($query);
            $declined = RequestStatus::DECLINE;
            $stmt->bind_param("ii", $declined, $request);
            $stmt->execute();
            $last_id = $db->insert_id;

            //return success
            $response->status = 200;
            $response->data = $last_id;
            $response->message = "Request successfully accepted.";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->data = $e->getMessage();
        }
        return $response;
    }

    public static function AcceptRequest($request){
        $response = new stdClass();
        if (empty($request)){
            $response->status = 400;
            $response->data = '';
            $response->message = "Invalid Request Id";
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
            //add to the request table
            $query = "UPDATE " . REQUEST_TABLE . " SET `status` = ? WHERE `id` = ?";
            $stmt = $db->prepare($query);
            $accepted = RequestStatus::APPROVED;
            $stmt->bind_param("ii", $accepted, $request);
            $stmt->execute();
            $last_id = $db->insert_id;

            //return success
            $response->status = 200;
            $response->data = $last_id;
            $response->message = "Request successfully accepted.";
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->data = $e->getMessage();
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
            $query = "UPDATE " . REQUEST_TABLE . " SET status = ? WHERE requester = ? AND status = ?";
            $stmt = $db->prepare($query);
            $initiated = RequestStatus::INITIATED;
            $declined = RequestStatus::DECLINE;
            $stmt->bind_param("iii", $declined, $userId, $initiated);
            $stmt->execute();

            //clean up any requests from other people
            $query = "UPDATE " . REQUEST_TABLE . " SET status = ? WHERE requestee = ? AND status = ?";
            $stmt = $db->prepare($query);
            $initiated = RequestStatus::INITIATED;
            $declined = RequestStatus::DECLINE;
            $stmt->bind_param("iii", $declined, $userId, $initiated);
            $stmt->execute();

            $response->data = '';
            $response->message = 'Request cleanup complete!';
        }catch(Exception $e){
            $response->status = 422;
            $response->data = '';
            $response->message = $e->getMessage();
        }
        return $response;
    }
}

?>