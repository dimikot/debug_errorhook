--TEST--
Debug_ErrorHook_TextNotifier: catch fatal errors
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$_SERVER = array("test" => "variable");
$_GET = array("get" => "var");
$_POST = array("post" => "var");
$_COOKIE = array("cookie" => "var");
$_SESSION = array("session" => "var");

non_existed_function();


?>
--EXPECT--
Fatal error: Call to undefined function non_existed_function() in * on line *
Text notification:
------------------
Subject: E_ERROR: Call to undefined function non_existed_function() at * on line *

E_ERROR: Call to undefined function non_existed_function()
at * on line *

SERVER:
    array(
        "test" => "variable",
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

