--TEST--
Debug_ErrorHook_RemoveDupsWrapper: no duplicated notifications should be sent
--FILE--
<?php
define("NODUPS_DELAY", 6);
require dirname(__FILE__) . '/init.php';

for ($i = 0; $i < 3; $i++) {
	if ($i) sleep(4);
	echo $a;
}

?>
--EXPECT--
Notification: array (
  'errno' => 'E_NOTICE',
  'errstr' => 'Undefined variable: a',
  'errfile' => '030_cnt.php',
  'errline' => '*',
  'tracecount' => 0,
)
Error [8]: Undefined variable: a in * on line *
Error [8]: Undefined variable: a in * on line *
Notification: array (
  'errno' => 'E_NOTICE',
  'errstr' => 'Undefined variable: a',
  'errfile' => '030_cnt.php',
  'errline' => '*',
  'tracecount' => 0,
  'prependText' => 'THIS ERROR HAPPENED 2 TIMES WITHIN LAST 0 MINUTES!
',
)
Error [8]: Undefined variable: a in * on line *
