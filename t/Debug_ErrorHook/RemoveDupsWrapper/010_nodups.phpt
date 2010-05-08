--TEST--
Debug_ErrorHook_RemoveDupsWrapper: no duplicated notifications should be sent
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

for ($i = 0; $i < 3; $i++) {
	echo $a;
}

?>
--EXPECT--
Notification: array (
  'errno' => 'E_NOTICE',
  'errstr' => 'Undefined variable: a',
  'errfile' => '010_nodups.php',
  'errline' => '*',
  'tracecount' => 0,
)

Notice: Undefined variable: a in * on line *

Notice: Undefined variable: a in * on line *

Notice: Undefined variable: a in * on line *

