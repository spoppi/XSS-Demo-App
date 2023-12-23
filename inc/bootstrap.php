<?php
define("PROJECT_ROOT_PATH", realpath(__DIR__ . "/.."));

// base path of the URI with added slashes
define("BASE_PATH", "/" . basename(PROJECT_ROOT_PATH) . "/");

// log file for errors, INSECURE hardcoded location prone to local attacks like symlink!
define("LOG_FILE", "./tmp/XssAuthToken.log");

//echo "Project root: " . PROJECT_ROOT_PATH;
//echo "Base path: " . BASE_PATH;

// include the base controller file
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";
?>
