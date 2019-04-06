<?php

namespace Amesplash\CampaignMonitorLog\Unit;

use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Eloquent\Phony\Phpunit\Phony;
use Amesplash\CampaignMonitorLog\LogDecorator;
use Amesplash\CampaignMonitorLog\Exception\InvalidArgumentException;

class LogDecoratorTest extends TestCase
{
    public function invalidLogLevels() : array
    {
        return [
            ['extreme'],
            ['systemdown'],
            ['100.00'],
            [600],
            ['SERVER_ERROR']
        ];
    }

    /**
     * @param mixed $ttl
     * @dataProvider invalidLogLevels
     * @throws \Exception
     */
    public function testItThrowsAnExceptionWithInvalidLogLevels($level)
    {
        $psrLogHandle = Phony::partialMock(LoggerInterface::class, null);
        $psrLogMock = $psrLogHandle->get();
        $psrLogDecorator = new LogDecorator($psrLogMock);

        $this->expectException(InvalidArgumentException::class);
        $psrLogDecorator->log_message('Log Message', 'PHPUnit Test', $level);
    }

    public function testItLogsAMessageWithGivenDefaultContext()
    {
        $defaultContext = [
            'Test Method' => 'test_it_logs_a_message_with_given_context',
            'Parent Module' => 'PHPUnit'
        ];

        $psrLogHandle = Phony::partialMock(LoggerInterface::class, null);

        $psrLogMock = $psrLogHandle->get();
        $psrLogDecorator = new LogDecorator($psrLogMock, $defaultContext);

        $psrLogDecorator->log_message('PHPunit Log Message', 'PHPUnit', 1000);


        $psrLogHandle->log->calledWith(
            'debug',
            'PHPunit Log Message',
            $defaultContext + ['module' => 'PHPUnit']
        );
    }
}
