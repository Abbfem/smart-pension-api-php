<?php

declare(strict_types=1);

namespace NestPension\Utils;

/**
 * Simple logger utility for NEST Pension library.
 */
class Logger
{
    private bool $enabled;
    private string $logLevel;
    private ?string $logFile;

    public const EMERGENCY = 'emergency';
    public const ALERT = 'alert';
    public const CRITICAL = 'critical';
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const NOTICE = 'notice';
    public const INFO = 'info';
    public const DEBUG = 'debug';

    private const LEVEL_PRIORITIES = [
        self::DEBUG => 0,
        self::INFO => 1,
        self::NOTICE => 2,
        self::WARNING => 3,
        self::ERROR => 4,
        self::CRITICAL => 5,
        self::ALERT => 6,
        self::EMERGENCY => 7,
    ];

    public function __construct(bool $enabled = true, string $logLevel = self::INFO, ?string $logFile = null)
    {
        $this->enabled = $enabled;
        $this->logLevel = $logLevel;
        $this->logFile = $logFile;
    }

    /**
     * Log an emergency message.
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Log an alert message.
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Log a critical message.
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Log an error message.
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log a warning message.
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log a notice message.
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Log an info message.
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log a debug message.
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Log a message with the given level.
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        if (!$this->shouldLog($level)) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] {$level}: {$message}{$contextString}" . PHP_EOL;

        if ($this->logFile) {
            file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        } else {
            echo $logMessage;
        }
    }

    /**
     * Check if message should be logged based on current log level.
     */
    private function shouldLog(string $level): bool
    {
        $currentPriority = self::LEVEL_PRIORITIES[$this->logLevel] ?? 1;
        $messagePriority = self::LEVEL_PRIORITIES[$level] ?? 0;

        return $messagePriority >= $currentPriority;
    }

    /**
     * Enable or disable logging.
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set the minimum log level.
     */
    public function setLogLevel(string $logLevel): self
    {
        $this->logLevel = $logLevel;
        return $this;
    }

    /**
     * Set the log file path.
     */
    public function setLogFile(?string $logFile): self
    {
        $this->logFile = $logFile;
        return $this;
    }

    /**
     * Check if logging is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
