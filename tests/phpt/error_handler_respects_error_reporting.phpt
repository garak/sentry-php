--TEST--
Test that the error handler ignores silenced errors by default, but it reports them with the appropriate option enabled.
--FILE--
<?php

declare(strict_types=1);

namespace Sentry\Tests;

use Sentry\ClientBuilder;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\Transport\TransportInterface;

$vendor = __DIR__;

while (!file_exists($vendor . '/vendor')) {
    $vendor = \dirname($vendor);
}

require $vendor . '/vendor/autoload.php';

$transport = new class implements TransportInterface {
    public function send(Event $event): ?string
    {
        echo 'Transport called' . PHP_EOL;

        return null;
    }
};

$client = ClientBuilder::create(['capture_silenced_errors' => true])
    ->setTransport($transport)
    ->getClient();

SentrySdk::getCurrentHub()->bindClient($client);

echo 'Triggering silenced error' . PHP_EOL;

@$a['missing'];

$client->getOptions()->setCaptureSilencedErrors(false);

echo 'Triggering silenced error' . PHP_EOL;

@$a['missing'];
?>
--EXPECT--
Triggering silenced error
Transport called
Triggering silenced error
