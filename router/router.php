<?php
include_once('../components/user.php');
include_once('../components/pulse.php');
include_once('../components/game.php');
include_once('../components/request.php');
include_once('../components/attack.php');
include_once('../components/ship.php');

class RouterResponse
{
    public $status;
    public $data;
    public $message;

    function __construct($responseStatus, $responseData, $responseMessage = '')
    {
        $this->status = $responseStatus;
        $this->data = $responseData;
        $this->message = $responseMessage;
    }
}

//NON-SESSION ENDPOINTS
DEFINE('LOGIN', 'login');
DEFINE('LOGOUT', 'logout');
DEFINE('REGISTER', 'register');
DEFINE('FORGOTPASSWORD', 'forgotpassword');

//SESSION REQUIRED ENDPOINTS
DEFINE('PLAYERPULSE', 'playerpulse'); //track who is still active

//GET THE CURRENT GAME
DEFINE('GETGAME', 'getgame');
DEFINE('CREATEGAME', 'creategame');

//deal with players
DEFINE('GETPLAYERS', 'getplayers');
DEFINE('SELECTOPPONENT', 'selectopponent');
DEFINE('MAKEREQUEST', 'makerequest');
DEFINE('CHECKRESPONSE', 'checkresponse');
DEFINE('CHECKMYREQUESTS', 'checkmyrequests');
DEFINE('DECLINEGAMEREQUEST', 'declinegamerequest');
DEFINE('ACCEPTGAMEREQUEST', 'acceptgamerequest');
DEFINE('CLEANUPREQUEST','cleanuprequest');
DEFINE('CHECKEXISTINGREQUESTS','checkexistingrequests');

//save ships
DEFINE('SAVESHIPS', 'saveships');
//check for the game starting
DEFINE('CHECKGAME', 'checkgame');

//set up personal broad
DEFINE('GETSHIPS', 'getships');
//get my board if refresh -- this will include damage
DEFINE('GETATTACKS', 'getattacks');
//deal with damage
DEFINE('ATTACKOPPONENT', 'attackopponent');

//deal with turns????
DEFINE('GETTURN', 'getturn');

$postInfo = $_POST;
$request = !empty($_GET['request']) ? $_GET['request'] : '';
$response = new RouterResponse(404, '');
$loggedIn = false;

$notLoggedInResponse = new RouterResponse(403, 'Not Logged In');
//check if logged in
$sessionValid = isset($_SESSION['valid']) ? $_SESSION['valid'] : false;
$sessionUser = isset($_SESSION['user']) ? $_SESSION['user'] : '';

if ($sessionValid && !empty($sessionUser)) {
    $loggedIn = true;
}

//TODO: make $response universal, update all the endpoints to use the same thing

if (!empty($request)) {
    $request = strtolower($request);
    if (!$loggedIn) {
        switch ($request) {
            case LOGIN:
                $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
                $password = !empty($_POST['password']) ? trim($_POST['password']) : '';
                $loginResponse = User::Login($login, $password);
                Pulse::HeartBeat();
                $response = new RouterResponse($loginResponse->status, $loginResponse->data, $loginResponse->message);
                break;
            case REGISTER:
                $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
                $password = !empty($_POST['password']) ? trim($_POST['password']) : '';
                $loginResponse = User::Register($login, $password);
                $response = new RouterResponse($loginResponse->status, $loginResponse->data);
                break;
            case FORGOTPASSWORD:
                break;
        }
    } else {
        //a user will pick their opponent, and the system will display that prompt
        switch ($request) {
            case LOGOUT:
                Pulse::Clear();
                $requestCleanup = Request::CleanUp();
                $gameCleanup = Game::CleanUp();
                $logoutResponse = User::Logout();
                $response = new RouterResponse($logoutResponse->status, $logoutResponse->data);
                break;
            case GETGAME:
                $gameResponse = Game::GetCurrentGame();
                $response = new RouterResponse($gameResponse->status, $gameResponse->data, $gameResponse->message);
                break;
            case CREATEGAME:
                $opponent = !empty($_POST['opponent']) ? trim($_POST['opponent']) : '';
                $gameResponse = Game::CreateGame($opponent);
                $response = new RouterResponse($gameResponse->status, $gameResponse->data, $gameResponse->message);
            case GETPLAYERS:
                $players = Pulse::GetPlayers();
                $response = new RouterResponse($players->status, $players->data);
                break;
            case MAKEREQUEST:
                $opponent = !empty($_POST['opponent']) ? trim($_POST['opponent']) : '';
                $makeRequest = Request::MakeRequest($opponent);
                $response = new RouterResponse($makeRequest->status, $makeRequest->data, $makeRequest->message);
                break;
            case CHECKRESPONSE:
                $gameRequest = !empty($_POST['gamerequest']) ? trim($_POST['gamerequest']) : '';
                $checkRequest = Request::CheckRequest($gameRequest);
                $response = new RouterResponse($checkRequest->status, $checkRequest->data, $checkRequest->message);
                break;
            case CHECKMYREQUESTS:
                $gameCheck = Request::GetRequests();
                $response = new RouterResponse($gameCheck->status, $gameCheck->data, $gameCheck->message);
                break;
            case DECLINEGAMEREQUEST:
                $requestId = !empty($_POST['requestid']) ? trim($_POST['requestid']) : '';
                $gameDecline = Request::DeclineRequest($requestId);
                $response = new RouterResponse($gameDecline->status, $gameDecline->data, $gameDecline->message);
                break;
            case ACCEPTGAMEREQUEST:
                $requestId = !empty($_POST['requestid']) ? trim($_POST['requestid']) : '';
                $gameAccept = Request::AcceptRequest($requestId);
                $response = new RouterResponse($gameAccept->status, $gameAccept->data, $gameAccept->message);
                break;
            case CLEANUPREQUEST:
                $requestId = !empty($_POST['requestid']) ? trim($_POST['requestid']) : '';
                $cleanup = Request::CleanUpRequest($requestId);
                $response = new RouterResponse($cleanup->status, $cleanup->data, $cleanup->message);
                break;
            case CHECKEXISTINGREQUESTS:
                $checkExistingRequest = Request::CheckExistingRequests();
                $response = new RouterResponse($checkExistingRequest->status, $checkExistingRequest->data, $checkExistingRequest->message);
                break;
            case SAVESHIPS:
                $gameId = !empty($_POST['gameId']) ? trim($_POST['gameId']) : '';
                $ships = !empty($_POST['ships']) ? trim($_POST['ships']) : '';
                $saveShips = Ship::SaveShips($gameId, $ships);
                $response = new RouterResponse($saveShips->status, $saveShips->data, $saveShips->message);
                break;
            case ATTACKOPPONENT:

                break;
            case CHECKGAME:
                //see if the game has started after ships submitted
                $gameId = !empty($_POST['gameId']) ? trim($_POST['gameId']) : '';
                $gameCheck = Game::CheckGameStart($gameId);
                $response = new RouterResponse($gameCheck->status, $gameCheck->data, $gameCheck->message);
                break;
            default:
                $response = $notLoggedInResponse;
                break;
        }
    }
}
echo json_encode($response);
?>