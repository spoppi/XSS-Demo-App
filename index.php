<?php
require __DIR__ . "/inc/bootstrap.php";

// simple login without database
session_start(); /* Starts the session */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// we use a subdir in the webroot for our PoC app
// if directly added below webroot decrease indices by 1
if(!isset($_SESSION['UserData']['Username'])){
    $uri[3] = 'user';
    $uri[4] = 'login';

} elseif ((isset($uri[3]) && $uri[3] != 'user') || !isset($uri[4])) {
    // show home page
    $uri[3] = 'user';
    $uri[4] = 'home';
}

require PROJECT_ROOT_PATH . "/Controller/Api/UserController.php";

$objFeedController = new UserController();
$strMethodName = $uri[4] . 'Action';
$objFeedController->{$strMethodName}();
?>
