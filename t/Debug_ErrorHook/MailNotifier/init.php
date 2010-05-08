<?php
require_once dirname(__FILE__) . "/../init.php";
require_once "Debug/ErrorHook/MailNotifier.php";

class TestMailPrintNotifier extends Debug_ErrorHook_MailNotifier
{
	protected function _mail()
	{
		$args = func_get_args();
		$args[1] = base64_decode(preg_replace('/^.*?\?B\?/s', '', $args[1]));
		printr($args);
	}
}

$printListener = new Debug_ErrorHook_Listener();
$printListener->addNotifier(new TestMailPrintNotifier("test@example.com", 0));
