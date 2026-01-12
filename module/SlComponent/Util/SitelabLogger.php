<?php

namespace SlComponent\Util;

use UserFilePath;

class SitelabLogger {
    public static function log($strLogText){
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $file = isset($bt[0]['file']) ? basename($bt[0]['file']) : 'unknown';
        $line = isset($bt[0]['line']) ? $bt[0]['line'] : '0';
        $className = isset($bt[1]['class']) ? $bt[1]['class'] : '';
        $type = isset($bt[1]['type']) ? $bt[1]['type'] : ''; // -> 또는 ::
        $methodName = isset($bt[1]['function']) ? $bt[1]['function'] : '';
        $callSite = $className ? ($className . $type . $methodName) : $methodName;
        $tag = "[{$file}:{$line}] {$callSite}";
        SitelabLogger::logger2($tag, $strLogText);
    }
    public static function logger2($fncName, $strLogText){
        $logFilename = UserFilePath::data()->getPathName()."/log/sitelab/sitelabDebug.log";
        SitelabLogger::logging($fncName,$logFilename);
        SitelabLogger::logging($strLogText,$logFilename);
    }
    public static function logger($strLogText){
        $logFilename = UserFilePath::data()->getPathName()."/log/sitelab/sitelabDebug.log";
        SitelabLogger::logging($strLogText,$logFilename);
    }

    public static function error($strLogText){
        $logFilename = UserFilePath::data()->getPathName()."/log/sitelab/error.log";
        SitelabLogger::logging($strLogText,$logFilename);
    }

    public static function loggerSql($strLogText){
        $logFilename = UserFilePath::data()->getPathName()."/log/sitelab/sql.log";
        SitelabLogger::logging($strLogText,$logFilename);
    }

    public static function loggerAutoDebug($strLogText){
        $logFilename = UserFilePath::data()->getPathName()."/log/sitelab/autoDebug.log";
        SitelabLogger::logging($strLogText,$logFilename);
    }

    public static function logging($strLogText,$logFileName){
        $logFile = fopen($logFileName, "a");
        if($logFile == false) return;
        fwrite($logFile, date('Y-m-d G:i:s'). '' .' - ' . print_r($strLogText, true) . "\n");
        fclose($logFile);
    }
}