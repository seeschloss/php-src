--TEST--
Bug #66606 (Sets HTTP_CONTENT_TYPE but not CONTENT_TYPE) - POST request
--INI--
allow_url_fopen=1
--SKIPIF--
<?php
include "skipif.inc"; 
?>
--FILE--
<?php
include "php_cli_server.inc";
php_cli_server_start('var_dump($_SERVER["CONTENT_TYPE"], $_SERVER["CONTENT_LENGTH"])');

list($host, $port) = explode(':', PHP_CLI_SERVER_ADDRESS);
$port = intval($port)?:80;

$fp = fsockopen($host, $port, $errno, $errstr, 0.5);
if (!$fp) {
  die("connect failed");
}

if (fwrite($fp, <<<HEADER
POST /index.php HTTP/1.1
Host: {$host}
Content-Type: application/x-www-form-urlencoded
Content-Length: 3

a=b
HEADER
)) {
	while (!feof($fp)) {
		echo fgets($fp);
	}
}

fclose($fp);
?>
--EXPECTF--	
HTTP/1.1 200 OK
Host: %s
Connection: close
Date: %s
X-Powered-By: PHP/%s
Content-type: text/html; charset=UTF-8

string(33) "application/x-www-form-urlencoded"
string(1) "3"
