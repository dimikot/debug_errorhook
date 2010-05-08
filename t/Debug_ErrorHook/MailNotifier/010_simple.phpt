--TEST--
Debug_ErrorHook_MailNotifier: simple test
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

function f() { echo $a; }
f();



?>
--EXPECT--
array (
  0 => 'test@example.com',
  1 => '[ERROR] E_NOTICE: Undefined variable: a at * on line *',
  2 => 'E_NOTICE: Undefined variable: a
at * on line *
',
  3 => 'From: test@example.com
Content-Type: text/plain; charset=UTF-8',
)

Notice: Undefined variable: a in * on line *

