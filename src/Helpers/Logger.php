<?php
/**
 * Filename: Logger.php
 * User: lucas
 * Date: 02/09/2021
 * Time: 02:12
 */

namespace Lucasgnunes\Galvwork\Helpers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogLogger;

class Logger
{
    private $log;

    public function __construct(string $fileName)
    {
        $this->log = new MonoLogLogger($fileName);
        $this->log->pushHandler(new StreamHandler(ROOT.'logs/'.$fileName.'.log'));
    }

    public static function log(string $fileName): Logger
    {
        return new static($fileName);
    }

    public function debug(string $message, array $context = []): MonoLogLogger
    {
        $this->log->debug($message, $context);

        return $this->log;
    }

    public function info(string $message, array $context = []): MonoLogLogger
    {
        $this->log->info($message, $context);

        return $this->log;
    }

    public function notice(string $message, array $context = []): MonoLogLogger
    {
        $this->log->notice($message, $context);

        return $this->log;
    }

    public function warning(string $message, array $context = []): MonoLogLogger
    {
        $this->log->warning($message, $context);

        return $this->log;
    }

    public function error(string $message, array $context = []): MonoLogLogger
    {
        $this->log->error($message, $context);

        return $this->log;
    }

    public function critical(string $message, array $context = []): MonoLogLogger
    {
        $this->log->critical($message, $context);

        return $this->log;
    }

    public function alert(string $message, array $context = []): MonoLogLogger
    {
        $this->log->alert($message, $context);

        return $this->log;
    }

    public function emergency(string $message, array $context = []): MonoLogLogger
    {
        $this->log->emergency($message, $context);

        return $this->log;
    }
}