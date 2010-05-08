--TEST--
Debug_ErrorHook_RemoveDupsWrapper: GC check
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

class TestRemoveDups extends Debug_ErrorHook_RemoveDupsWrapper
{
	protected function _getGcProbability()
	{
		return 1;
	}
}

// Hmm... without this line PHP calls $printListenerWithNoDups's destructor
// AFTER reassignment. Possibly it is correct: if an exception is
// raised at Debug_ErrorHook_Listener, $printListenerWithNoDups will
// stay unchanged. 
$printListenerWithNoDups = null;

$printListenerWithNoDups = new Debug_ErrorHook_Listener();
$printListenerWithNoDups->addNotifier(new TestRemoveDups(new PrintNotifier(), 'fixture', "0e0"));

echo $a;

print_r(glob("fixture/*"));

?>
--EXPECT--
Notification: array (
  'errno' => 'E_NOTICE',
  'errstr' => 'Undefined variable: a',
  'errfile' => '020_gc.php',
  'errline' => '*',
  'tracecount' => 0,
)

Notice: Undefined variable: a in * on line *
Array
(
)
