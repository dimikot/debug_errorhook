--TEST--
Debug_ErrorHook_Catcher: unhandled exception
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

function f() {
	throw new Exception("unhandled");
}

f();


?>

--EXPECT--
Fatal error: Uncaught exception 'Exception' with message 'unhandled' in *:*
Stack trace:
#0 *(*): f()
#1 {main}
  thrown in * on line *
Notification: array (
  'errno' => 'E_ERROR',
  'errstr' => 'Uncaught exception \'Exception\' with message \'unhandled\' in *:*
Stack trace:
#0 *(*): f()
#1 {main}
  thrown',
  'errfile' => '040_exception.php',
  'errline' => '*',
  'tracecount' => 0,
)

