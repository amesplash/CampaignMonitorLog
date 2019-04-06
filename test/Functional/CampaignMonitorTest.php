<?php

namespace Amesplash\CampaignMonitorLog\Functional;

use CS_REST_Campaigns;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Eloquent\Phony\Phpunit\Phony;
use Amesplash\CampaignMonitorLog\LogDecorator;

class CampaignMonitorTest extends TestCase
{
    public function testItCanBeUsedByCMClasses()
    {
        $psrLogHandle = Phony::partialMock(LoggerInterface::class, null);
        $psrLogMock = $psrLogHandle->get();
        $psrLogDecorator = new LogDecorator($psrLogMock);

        $subscriber = new CS_REST_Campaigns(
            'campaign_id',
            ['api_key' => 'api-key'],
            'https',
            CS_REST_LOG_VERBOSE,
            'api.createsend.com',
            $psrLogDecorator
        );

        $psrLogHandle->log->calledWith(
            'debug',
            'Creating wrapper for https://api.createsend.com/api/v3.2/',
            ['module' => 'CS_REST_Campaigns']
        );

        $psrLogHandle->log->calledWith(
            'warning',
            'Using cURL for transport',
            ['module' => 'CS_REST_Campaigns']
        );

        $psrLogHandle->log->calledWith(
            'warning',
            'Using native json serialising',
            ['module' => 'CS_REST_Campaigns']
        );

        $psrLogHandle->log->calledWith(
            'debug',
            'Getting serialiser',
            ['module' => 'CS_REST_SERIALISATION_get_available']
        );

        $subscriber->create('client_id', ['Subject' => 'Test']);

        $psrLogHandle->log->calledWith(
            'debug',
            "Call result: <pre>array (\n  'code' => 401,\n  'response' => '{\"Code\":50,\"Message\":\"Must supply a valid HTTP Basic Authorization header\"}',\n)</pre>",
            ['module' => 'CS_REST_Campaigns']
        );
    }
}
