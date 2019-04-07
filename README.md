# Campaign Monitor PSR-3 Log Decorator

[![Build Status](https://travis-ci.com/amesplash/CampaignMonitorLog.svg?branch=master)](https://travis-ci.com/amesplash/CampaignMonitorLog)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/amesplash/CampaignMonitorLog/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/amesplash/CampaignMonitorLog/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/amesplash/CampaignMonitorLog/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/amesplash/CampaignMonitorLog/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/amesplash/campaignmonitor-log/v/stable)](https://packagist.org/packages/amesplash/campaignmonitor-log)
[![License](https://poser.pugx.org/amesplash/campaignmonitor-log/license)](https://packagist.org/packages/amesplash/campaignmonitor-log)

[PSR-3 Log](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
decorator for Campaign Monitor.

## Installation

```bash
$ composer require amesplash/campaignmonitor-log
```

If not already available the above command will also install `campaignmonitor/createsend-php`.

## Usage

Construct a new LogDecorator instance with any PSR-3 compatible logger. You can pass through your default context as the second argument.

Construct your Campaign Monitor instances passing your desired log level and the LogDecorator instance.

```php
<?php
declare(strict_types=1);

namespace App;

use CS_REST_Campaigns;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Amesplash\CampaignMonitorLog\LogDecorator;

final class MyCampaignMonitorCampaignFactory
{
    public function __invoke(ContainerInterface $container) : CS_REST_Campaigns
    {
        $params = $container->get('params');
        $psr3Logger = $container->get('monolog or any other PSR 3 Logger');

        $defaultContext = [
            'Campaign' => 'Functional Dev',
        ];

        $logDecorator = new LogDecorator($psr3Logger, $defaultContext);

        return new CS_REST_Campaigns(
            $params->get('campaignmonitor.campaign_id'),
            ['api_key' => $params->get('campaignmonitor.api_key')],
            'https',
            CS_REST_LOG_VERBOSE,
            'api.createsend.com',
            $logDecorator
        );
    }
}
```

### Log Levels

This library maps the Camapign Monitor log levels defined in `vendor/campaignmonitor/class/log.php` as follows:

| Campaign Monitor Log Level | PSR Log Level      |
| -------------------------- | ------------------ |
| CS_REST_LOG_VERBOSE (1000) | LogLevel::DEBUG    |
| CS_REST_LOG_WARNING (500)  | LogLevel::WARNING  |
| CS_REST_LOG_ERROR (250)    | LogLevel::ERROR    |

_If your log level is `CS_REST_LOG_NONE` it will be pointless to instantiate the LogDecorator._
