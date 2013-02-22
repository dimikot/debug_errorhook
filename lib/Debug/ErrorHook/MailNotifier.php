<?php
/**
 * Sends all notifications to a specified email.
 * 
 * Consider using this class together with Debug_ErrorHook_RemoveDupsWrapper
 * to avoid mail server flooding when a lot of errors arrives. 
 */

require_once "Debug/ErrorHook/Util.php";
require_once "Debug/ErrorHook/TextNotifier.php";

class Debug_ErrorHook_MailNotifier extends Debug_ErrorHook_TextNotifier
{
    private $_to;
    private $_from;
    private $_charset;
    private $_whatToSend;
    private $_subjPrefix;

    public function __construct($to, $whatToSend, $subjPrefix = "[ERROR] ", $charset = "UTF-8", $from = null)
    {
        parent::__construct($whatToSend);
        $this->_to = $to;
        $this->_from = $from? $from : $to;
        $this->_subjPrefix = $subjPrefix;
        $this->_charset = $charset;
    }

    protected function _notifyText($subject, $body)
    {
        $msgId = md5(__CLASS__ . $this->_to . $this->_from . $this->_subjPrefix . $subject) . "@errorhook";
        $this->_mail(
           $this->_to,
           $this->_encodeMailHeader($this->_subjPrefix . $subject),
           $body,
           join("\r\n", array(
               "From: {$this->_from}",
               "Content-Type: text/plain; charset={$this->_charset}",
               "Message-Id: <$msgId>",
               "In-Reply-To: <$msgId>",
           ))
        );
    }

    protected function _mail()
    {
        $args = func_get_args();
        @call_user_func_array("mail", $args);
    }

    private function _encodeMailHeader($header) 
    {
        return preg_replace_callback(
            '/((?:^|>)\s*)([^<>]*?[^\w\s.][^<>]*?)(\s*(?:<|$))/s',
            array($this, '_encodeMailHeaderCallback'),
            $header
        );
    }

    public function _encodeMailHeaderCallback($p)
    {
        $encoding = $this->_charset;
        return $p[1] . "=?$encoding?B?" . base64_encode($p[2]) . "?=" . $p[3];
    }    
}
