<?php
/**
 * Wrapper which denies duplicated notifications to be
 * processed again and again. It is needed to lower the
 * traffic to mail server in case the site is down. 
 * 
 * This class stores meta-informations in filesystem.
 * It takes care about garbage collecting.
 */

require_once "Debug/ErrorHook/INotifier.php";

class Debug_ErrorHook_RemoveDupsWrapper implements Debug_ErrorHook_INotifier
{
    const DEFAULT_NO_REPEAT_PERIOD = 7200; // in seconds
    const ERROR_FILE_SUFFIX = ".error";
    const GC_PROBABILITY = 0.001;

    private $_notifier;
    private $_tmpPath;
    private $_period;
    private $_gcExecuted = false;

    public function __construct(Debug_ErrorHook_INotifier $notifier, $tmpPath = null, $period = null)
    {
        $this->_tmpPath = $tmpPath? $tmpPath : $this->_getDefaultTmpPath();
        $this->_period = $period? $period : self::DEFAULT_NO_REPEAT_PERIOD;
        $this->_notifier = $notifier;
        if (!file_exists($this->_tmpPath) || !is_dir($this->_tmpPath)) {
            if (!@mkdir($this->_tmpPath, 0777, true)) {
                $error = error_get_last();
                throw new Exception("Cannot create '{$this->_tmpPath}': {$error['message']}");
            }
            @chmod($this->_tmpPath, 0777);
        }
    }

    public function notify($errno, $errstr, $errfile, $errline, $trace, $hash = null, $prependText = null)
    {
        $text = $errno . " in " . $errfile . " on line " . $errline . "\n";
        if (is_array($trace)) {
            $text .= "Stack trace:\n";
            foreach ($trace as $i => $item) {
                $text .= "#" . $i . " " . (@$item['file']? $item['file'] . "(" . $item['line'] . ")" : '[internal function]') . ": " . @$item['class'] . @$item['type'] . $item['function'] . "\n";
            }
        }
        $hash = md5($text);
        // Error message is NOT included in $hash, because it varies heavily!!
        // But for debug reasons it's quite handy to have the text included
        // in the lock file contents.
        $text = "$errstr\n" . $text;
        $lastNotifyTime = $this->_getLastTouchTime($hash);
        if (time() - $lastNotifyTime > $this->_period) {
            $cnt = $this->_getCnt($hash);
            if ($cnt > 1 && $lastNotifyTime) {
                $prependText =
                    sprintf("THIS ERROR HAPPENED %d TIMES WITHIN LAST %d MINUTES!\n", $cnt, (time() - $lastNotifyTime) / 60)
                    . ($prependText? "\n" . $prependText : "");
            }
            $this->_notifier->notify($errno, $errstr, $errfile, $errline, $trace, $hash, $prependText);
            // Touch & reset counter when we've called a notification (e.g. sent a mail).
            $this->_touch($hash, $text);
            $this->_incCnt($hash, true);
            $this->_gc();
        } else {
            // Just increment error counter.
            $this->_incCnt($hash);
        }
    }

    protected function _getDefaultTmpPath()
    {
        return sys_get_temp_dir() . "/" . get_class($this);
    }

    protected function _getGcProbability()
    {
        // This method is "protected" for unit-test overrides.
        return self::GC_PROBABILITY;
    }

    private function _getLockFname($hash)
    {
        return $this->_tmpPath . '/' . $hash . self::ERROR_FILE_SUFFIX;
    }

    private function _getCntFname($hash)
    {
        return $this->_tmpPath . '/' . $hash . ".cnt" . self::ERROR_FILE_SUFFIX;
    }

    private function _getLastTouchTime($hash)
    {
        $file = $this->_getLockFname($hash);
        return file_exists($file)? filemtime($file) : 0;
    }

    private function _touch($hash, $text)
    {
        $file = $this->_getLockFname($hash);
        file_put_contents($file, $text);
        @chmod($file, 0666);
        clearstatcache();
    }

    private function _getCnt($hash)
    {
        $file = $this->_getCntFname($hash);
        $f = @fopen($file, "r");
        if (!$f) {
            return 1;
        }
        flock($f, LOCK_SH);
        $cnt = trim(fgets($f));
        flock($f, LOCK_UN);
        fclose($f);
        return $cnt;
    }

    private function _incCnt($hash, $resetCounter = false)
    {
        $file = $this->_getCntFname($hash);
        fclose(fopen($file, "a+")); // create empty
        $f = fopen($file, "r+");
        flock($f, LOCK_EX);
        $cnt = trim(fgets($f));
        if ($resetCounter) {
            $cnt = 0;
        }
        $cnt++;
        fseek($f, 0, SEEK_SET);
        ftruncate($f, 0);
        fwrite($f, $cnt . "\n");
        flock($f, LOCK_UN);
        fclose($f);
        @chmod($file, 0666);
    }

    private function _gc()
    {
        if ($this->_gcExecuted || mt_rand(0, 10000) >= $this->_getGcProbability() * 10000) {
            return;
        }
        foreach (glob("{$this->_tmpPath}/*" . self::ERROR_FILE_SUFFIX) as $file) {
            if (filemtime($file) <= time() - $this->_period * 2) {
                @unlink($file);
            }
        }
        $this->_gcExecuted = true;
    }
}
