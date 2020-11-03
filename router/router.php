<?php
include_once('../components/user.php');

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

$postInfo = $_POST;
$request = !empty($_GET['request']) ? $_GET['request'] : '';
$response = new RouterResponse(404,'');

if (!empty($request)) {
    $request = strtolower($request);
    switch($request){
        case LOGIN:
            $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
            $password = !empty($_POST['password']) ? trim($_POST['password']) : '';
            $loginResponse = User::Login($login, $password);
            $response = new RouterResponse($loginResponse->status,$loginResponse->data);
            break;
        case LOGOUT:
            $user = new User();
            $logoutResponse = User::Logout();
            $response = new RouterResponse($logoutResponse->status, $logoutResponse->data);
            break;
    }
}
echo json_encode($response);
?>