<?php

namespace App\Core;

class Logger
{
    /**
     * Write an error log to storage/logs/errors.log
     */
    public static function error(string $message): void
    {
        $logDir = __DIR__ . '/../../storage/logs';
        $logFile = $logDir . '/errors.log';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $formatted = "[$timestamp] ERROR: $message" . PHP_EOL;

        file_put_contents($logFile, $formatted, FILE_APPEND);
    }

    /**
     * Info log (optional for debugging)
     */
    public static function info(string $message): void
    {
        $logDir = __DIR__ . '/../../storage/logs';
        $logFile = $logDir . '/info.log';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $formatted = "[$timestamp] INFO: $message" . PHP_EOL;

        file_put_contents($logFile, $formatted, FILE_APPEND);
    }
}
