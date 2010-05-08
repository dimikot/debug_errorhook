--TEST--
Debug_ErrorHook_Catcher: simple trigger_error()
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

trigger_error("test");

?>
--EXPECT--
Notification: array (
  'errno' => 'E_USER_NOTICE',
  'errstr' => 'test',
  'errfile' => '010_simple.php',
  'errline' => '*',
  'tracecount' => 1,
)

Notice: test in * on line *

