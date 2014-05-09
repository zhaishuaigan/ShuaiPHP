<?php

class Log {

    const ERR = 0;
    const INFO = 1;
    const WARN = 2;
    const DEBUG = 3;

    static function Write($msg, $type = self::DEBUG) {
        switch ($type) {
            case self::ERR :
                echo "<p style=' color:#FF0000'>$msg</p>";
                break;
            case self::INFO :
                echo "<p style=' color:#00FF00'>$msg</p>";
                break;
            case self::WARN :
                echo "<p style=' color:#999900'>$msg</p>";
                break;
            case self::DEBUG :
                echo "<p style=' color:#000000'>$msg</p>";
                break;
        }
    }

}
