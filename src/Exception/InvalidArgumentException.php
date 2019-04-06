<?php
declare(strict_types = 1);

namespace Amesplash\CampaignMonitorLog\Exception;

class InvalidArgumentException extends \Psr\Log\InvalidArgumentException
{
    public static function fromInvalidLogLevel(
        $level,
        $allowedTypes = [1000, 500, 250]
    ) : self {
        $argument = is_object($level) ? get_class($level) : gettype($level);

        $message = sprintf(
            'Argument "%s" is invalid. Allowed log levels are "%s".',
            $argument,
            implode(', ', $allowedTypes)
        );

        return new self($message);
    }
}
