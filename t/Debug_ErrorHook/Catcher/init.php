<?php
require_once dirname(__FILE__) . "/../init.php";

$printListener = new Debug_ErrorHook_Listener();
$printListener->addNotifier(new PrintNotifier());
