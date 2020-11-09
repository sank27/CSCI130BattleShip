<?php
include_once('../components/user.php');
include_once('../components/pulse.php');

class RouterResponse {
    public $status;
    public $data;

    function __construct($responseStatus, $responseData){
        $this->status = $responseStatus;
        $this->data = $responseData;
    }
}

DEFINE('LOGIN', 'login');
DEFINE('LOGOUT', 'logout');
DEFINE('REGISTER', 'register');
DEFINE('FORGOTPASSWORD', 'forgotpassword');


//SESSION REQUIRED ENDPOINTS
DEFINE('PLAYERPULSE','playerpulse'); //track who is still active
//deal with players
DEFINE('GETPLAYERS', 'getplayers');
DEFINE('SELECTOPPONENT', 'selectopponent');

//set up personal broad
DEFINE('SETMYBOARD','setmyboard');
//get my board if refresh -- this will include damage
DEFINE('GETMYBOARD','getmyboard');
//get enemy board -- this will include damage
DEFINE('GETOPPONENTBOARD','getopponentboard');
//deal with damage
DEFINE('ATTACKOPPONENT','attackopponent');

//deal with turns????
DEFINE('GETTURN', 'getturn');

$postInfo = $_POST;
$request = !empty($_GET['request']) ? $_GET['request'] : '';
$response = new RouterResponse(404,'');
$loggedIn = false;

$notLoggedInResponse = new RouterResponse(403 ,'Not Logged In');
//check if logged in
if ($_SESSION['valid'] && !empty($_SESSION['user'])){
    $loggedIn = true;
}

if (!empty($request)) {
    $request = strtolower($request);
    switch($request){
        case LOGIN:
            $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
            $password = !empty($_POST['password']) ? trim($_POST['password']) : '';
            $loginResponse = User::Login($login, $password);
            Pulse::HeartBeat();
            $response = new RouterResponse($loginResponse->status,$loginResponse->data);
            break;
        case LOGOUT:
            Pulse::Clear();
            $logoutResponse = User::Logout();
            $response = new RouterResponse($logoutResponse->status, $logoutResponse->data);
            break;
        case REGISTER:
            $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
            $password = !empty($_POST['password']) ? trim($_POST['password']) : '';
            $loginResponse = User::Register($login, $password);
            $response = new RouterResponse($loginResponse->status,$loginResponse->data);
            break;
        case FORGOTPASSWORD:
            break;
        case PLAYERPULSE:
            if (!$loggedIn) {
                $response = $notLoggedInResponse;
                break;
            }


            break;
        case GETPLAYERS:
            if (!$loggedIn) {
                $response = $notLoggedInResponse;
                break;
            }

            break;
        case SELECTOPPONENT:
            if (!$loggedIn){
                $response = $notLoggedInResponse;
                break;
            }

            break;
    }
}
echo json_encode($response);
?>