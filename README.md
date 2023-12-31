# Intentionally vulnerable PHP website for article *The Underestimated Dangers of Cross-Site Scripting*

This simple (ugly) project was created for accompanying my article about *The Understimated Dangers of Cross-Site Scripting* at [Medium](https://medium.com/@spoppi/the-underestimated-dangers-of-cross-site-scripting-1cbbf7b62686). It was created solely for demonstation purposes.

It is vulnerable to (at least) cross-site scripting (XSS) in the http parameter `msg` as well as via log injection and leaks (pseudo) authentication token via response header.

**DON'T USE IN PRODUCTION ENVIRONMENTS**

**THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO ANY WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT OF COPYRIGHT,PATENT, TRADEMARK, OR OTHER RIGHT. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, INCLUDING ANY GENERAL, SPECIAL, INDIRECT, INCIDENTAL, OR CONSEQUENTIAL DAMAGES, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF THE USE OR INABILITY TO USE THE SOFTWARE OR FROM OTHER DEALINGS IN THE SOFTWARE.**

**USE AT OWN RISK**

## Installation
Simply create a subdirectory below your sever's root directory (e.g. `/var/www/html/subdir` for Apache httpd) and extract the project. Make sure your webserver supports PHP (for Apache e.g. `a2enmod php8.2`).

Create a subdir named `tmp` and make it accessible and writeable for `www-data` (or whatever user and group your webserver uses):
* mkdir tmp
* chown www-data:www-data tmp
* chmod 750 tmp

## Usage
https://server/subdir/index.php		Initally calls the login form, else opens the home page

https://server/subdir/index.php?msg=add_xss_payload_here

The app makes use of the API endpoints provided below.

## API Endpoints
https://server/subdir/index.php/user/login

	User login with hardcoded passwords (no database, see `Controller/Api/UserController.php`)

https://server/subdir/index.php/user/home

	A simple homepage, The parameter `msg` is prone to XSS.
	
https://server/subdir/index.php/user/token

	Get the user's authentication token.

https://server/subdir/index.php/user/info

	Get all entries in the log file for a given user. Prone to XSS via log injection.
	Requires admin login.

https://server/subdir/index.php/user/logout

	logout 
