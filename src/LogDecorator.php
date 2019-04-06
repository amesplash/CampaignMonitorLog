<?php
declare(strict_types=1);

namespace Amesplash\CampaignMonitorLog;

use Psr\Log\LogLevel;
use Psr\Log\LoggerTrait;
use Psr\Log\LoggerInterface;
use Amesplash\CampaignMonitorLog\Exception\InvalidArgumentException;

final class LogDecorator implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var string
     */
    private $logLevel;

    /**
     * CS_REST_LOG_VERBOSE = 1000,
     * CS_REST_LOG_WARNING = 500,
     * CS_REST_LOG_ERROR = 250
     * As defined by CS_Rest_Log class.
     *
     * @var array
     */
    private $campaignMonitorLogLevels = [
        1000 => LogLevel::DEBUG,
        500 => LogLevel::WARNING,
        250 => LogLevel::ERROR,
    ];

    /**
     * New Log Adapter instance.
     *
     * @param LoggerInterface $log
     * @param array           $context The default context
     */
    public function __construct(LoggerInterface $log, array $context = [])
    {
        $this->log = $log;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
    */
    public function log($level, $message, array $context = array())
    {
        $this->log->log($level, $message, $this->context + $context);
    }

    /**
     * The CS_Rest_Log log message method.
     * I know it sucks but nothing we can do!
     *
     * @var string $message
     * @var string $module
     * @var int $level CS_Rest_Log level
     * @return void
    */
    public function log_message($message, $module, $level) : void
    {
        $level = $this->translateLogLevel($level);
        $this->log($level, $message, ['module' => $module]);
    }

    /**
     * Translates CM_REST_Log levels to PSR-3 Log levels
     *
     * @param  mixed $level
     * @return string
     * @throws InvalidArgumentException
     */
    private function translateLogLevel($level) : string
    {
        if (!array_key_exists($level, $this->campaignMonitorLogLevels)) {
            throw InvalidArgumentException::fromInvalidLogLevel($level);
        }

        return $this->campaignMonitorLogLevels[$level];
    }
}
