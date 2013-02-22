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

--EXPECTF--
%sUncaught exception 'Exception' with message 'unhandled'%s
Stack trace:
#0 %s
#1 %s
  thrown in %s
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

