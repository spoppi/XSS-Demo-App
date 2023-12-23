<?php

class UserController extends BaseController
{
    /*
     * /user/login Endpoint
     * Simple login form based on https://www.w3schools.in/php/examples/php-login-without-using-database.
     * Writes login errors to local file including username (INSECURELY!)
     */
    public function loginAction()
    {
        //session_start(); /* Starts the session */

        /* Check Login form submitted */
        if(isset($_POST['Submit'])){
            /* Define username and associated password array */
            $logins = array('alice' => '123456','bob' => 'password1','admin' => 'badmin');

            /* Check and assign submitted Username and Password to new variable */
            $Username = isset($_POST['Username']) ? $_POST['Username'] : '';
            $Password = isset($_POST['Password']) ? $_POST['Password'] : '';

            /* Check Username and Password existence in defined array */
            if (isset($logins[$Username]) && $logins[$Username] == $Password){
                /* Success: Set session variables and redirect to Protected page  */
                $_SESSION['UserData']['Username'] = $Username;
                // create a random string of certain length
                $_SESSION['UserData']['AuthenticationToken'] = base64_encode(random_bytes(64));
                header("location: " . BASE_PATH . "index.php");
                exit;
            } else {
                /* Unsuccessful attempt: Set error message */
                $msg="<span style='color:red'>Invalid Login Details</span>";

                // log error including username
                $logmsg = "Invalid login for user `" . $Username . "`\n"; // log injection via username
                file_put_contents(LOG_FILE, $logmsg, FILE_APPEND | LOCK_EX);
            }
        }

        echo '<!doctype html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>Understimated XSS - Login</title>
            <link href="./css/style.css" rel="stylesheet">
            </head>
            <body>
            <div id="Frame0">
            <h1>Understimated XSS - Login</h1>
            </div>
            <br>
            <form action="" method="post" name="Login_Form">
            <table width="400" border="0" align="center" cellpadding="5" cellspacing="1" class="Table">';
        if(isset($msg)){
            echo '<tr>
                <td colspan="2" align="center" valign="top">' . $msg . '</td>
                </tr>';
        }

        echo '<tr>
                <td colspan="2" align="left" valign="top"><h3>Login</h3></td>
                </tr>
                <tr>
                <td align="right" valign="top">Username</td>
                <td><input name="Username" type="text" class="Input"></td>
                </tr>
                <tr>
                <td align="right">Password</td>
                <td><input name="Password" type="password" class="Input"></td>
                </tr>
                <tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="submit" value="Login" class="Button3"></td>
                </tr>
            </table>
            </form>
            </body>
            </html>';
    }

    public function logoutAction()
    {
        session_destroy();
        header("location: " . BASE_PATH . "index.php");  /* Redirect to login page */
        exit;
    }

    /*
     * "/user/token" Endpoint - Get authentication token of users
     */
    public function tokenAction()
    {
        // hard coded secret token for PoC, would be generated through secure means in a real world app ;)
        $token = $_SESSION['UserData']['AuthenticationToken'];
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $strAuthHeader = '';

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $responseData = '';
                $strAuthHeader = 'AuthenticationToken: ' . $token;

            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: text/plain', 'HTTP/1.1 200 OK', $strAuthHeader)
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    /*
     * /user/home endpoint - This has XSS in parameter `msg` by intention!
     */
    public function homeAction()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);

        $msg = 'Use the parameter `msg=<message>` to add an own message';

        if (isset($query['msg']))
        {
            // !!! XSS !!!
            $msg = $query['msg'];
        }

        $footer = '';

        // admin get's an additional lookup user button
        if ($_SESSION['UserData']['Username'] == 'admin')
        {
            $footer = '<br><br><a class="Button3" href="' . BASE_PATH . 'index.php/user/info">Lookup User</a>';
        }

        // simple dummy page
        $header  = '<!doctype html><html><head><link href="./css/style.css" rel="stylesheet"></head><body>';
        $content = '<title>Understimated XSS</title><div id="Frame1"><h1>Understimated XSS</h1></div><br><br><div id="Message">Your message: ' . $msg . '</div>';
        $footer .= '<br><br><a class="Button3" href="' . BASE_PATH . 'index.php/user/logout">Logout</a></body></html>';

        $this->sendOutput($header.$content.$footer,array('Content-Type: text/html', 'HTTP/1.1 200 OK'));
    }

    /*
     * /user/info
     * get the information from the logfile for the given user
     * This function is again intentionally prone to XSS!
     */
    public function infoAction()
    {
        //echo "user: " . $_SESSION['UserData']['Username'];

        if ($_SESSION['UserData']['Username'] != 'admin')
        {
            $msg = "Nice try ;) You need to be admin for user information.";
            header("Location: " . BASE_PATH . "index.php?msg=" . $msg);
            exit;
        }

        $user = '';
        $hidden = false;

        if(isset($_POST['Submit'])){
            $user = isset($_POST['Username']) ? $_POST['Username'] : '';
            // no error checking, am too lazy

            $hidden = true; // hide the input form
        }

        echo '<!doctype html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>Understimated XSS - User Info</title>
            <link href="../../css/style.css" rel="stylesheet">
            </head>
            <body>
            <div id="Frame0">
            <h1>Understimated XSS - User Info</h1>
            </div>
            <br>
            <table width="400" border="0" align="center" cellpadding="5" cellspacing="1" class="Table">';

        if (!$hidden) {
            echo '<form action="" method="post" name="User_Form">
                <tr>
                <td align="right" valign="top">Username</td>
                <td><input name="Username" type="text" class="Input"></td>
                </tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="submit" value="User" class="Button3"></td>
                </tr>
                </form>';
        } else {
            // search for the user's entries
            $re = '/^.+`('.$user.')`.*$/i';

            $fp = fopen(LOG_FILE, "r");
            if ($fp) {
                while (($buffer = fgets($fp, 4096)) !== false) {
                    //echo "Line read from " . LOG_FILE . ": " . $buffer;
                    if (preg_match ($re, $buffer, $match))
                    {
                        echo '<tr>
                            <td colspan="2" align="center" valign="top">' . $user . '</td>
                            <td colspan="2" align="center" valign="top">' . $match[0] . '</td>
                            </tr>';
                    }
                }

                fclose($fp);
            } else {
                echo '<tr>
                    <td colspan="2" align="center" valign="top">&nbsp;</td>
                    <td colspan="2" align="center" valign="top">No data</td>
                    </tr>';
            }
        }

        echo '</table>
            </body>
            </html>';
    }

}
