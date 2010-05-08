--TEST--
Debug_ErrorHook_Catcher: fatal error
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

non_existed_function();

?>

--EXPECT--
Fatal error: Call to undefined function non_existed_function() in * on line *
Notification: array (
  'errno' => 'E_ERROR',
  'errstr' => 'Call to undefined function non_existed_function()',
  'errfile' => '030_fatal.php',
  'errline' => '*',
  'tracecount' => 0,
)
