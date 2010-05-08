<?php
require_once dirname(__FILE__) . "/../init.php";
require_once "Debug/ErrorHook/TextNotifier.php";

class TestTextPrintNotifier extends Debug_ErrorHook_TextNotifier
{
	protected function _notifyText($subject, $body)
	{
		echo "Text notification:\n";
		echo "------------------\n";
		echo "Subject: $subject\n\n";
		echo $body;
	}
}

$printListener = new Debug_ErrorHook_Listener();
$printListener->addNotifier(new TestTextPrintNotifier(TestTextPrintNotifier::LOG_ALL));
