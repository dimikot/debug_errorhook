--TEST--
Debug_ErrorHook_TextNotifier: make text version of a notification
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER = array("test" => "variable", "HTTP_HOST" => "example.com", "REQUEST_URI" => "/some/page?args", "GATEWAY_INTERFACE" => "CGI");
$_GET = array("get" => "var");
$_POST = array("post" => "var");
$_COOKIE = array("cookie" => "var");
$_SESSION = array("session" => "var");

function f() { echo $a; }
f();



?>
--EXPECT--
Text notification:
------------------
Subject: E_NOTICE: Undefined variable: a at * on line *

//example.com/some/page?args
E_NOTICE: Undefined variable: a
at * on line *

TRACE:
    #0  f() called at [*:*]

SERVER:
    array(
        "test" => "variable",
        "HTTP_HOST" => "example.com",
        "REQUEST_URI" => "/some/page?args",
        "GATEWAY_INTERFACE" => "CGI",
    )

COOKIES:
    array(
        "cookie" => "var",
    )

GET:
    array(
        "get" => "var",
    )

POST:
    array(
        "post" => "var",
    )

SESSION:
    array(
        "session" => "var",
    )
Error [8]: Undefined variable: a in * on line *

