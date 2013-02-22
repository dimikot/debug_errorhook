--TEST--
Debug_ErrorHook_Catcher: fatal error
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

non_existed_function();

?>

--EXPECTF--
%sCall to undefined function non_existed_function()%s
Notification: array (
  'errno' => 'E_ERROR',
  'errstr' => 'Call to undefined function non_existed_function()',
  'errfile' => '030_fatal.php',
  'errline' => '*',
  'tracecount' => 0,
)
