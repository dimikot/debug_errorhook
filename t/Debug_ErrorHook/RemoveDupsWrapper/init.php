<?php
require_once dirname(__FILE__) . "/../init.php";
require_once "Debug/ErrorHook/RemoveDupsWrapper.php";

function cleanupTmp()
{
	$dir = "fixture";
	foreach (glob("$dir/*") as $f) unlink($f);
	rmdir($dir);
}

$printListenerWithNoDups = new Debug_ErrorHook_Listener();
$printListenerWithNoDups->addNotifier(new Debug_ErrorHook_RemoveDupsWrapper(new PrintNotifier(), 'fixture', 100));

register_shutdown_function("cleanupTmp");
