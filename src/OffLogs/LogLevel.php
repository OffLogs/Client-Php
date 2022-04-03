<?php

namespace OffLogs;

class LogLevel
{
    const Error = 'E';
    const Warning = 'W';
    const Fatal = 'F';
    const Information = 'I';
    const Debug = 'D';

    static $levels = [
        self::Error,
        self::Warning,
        self::Fatal,
        self::Information,
        self::Debug,
    ];

    public static function isCorrect(string $logLevel): bool
    {
        return in_array($logLevel, self::$levels);
    }
}
