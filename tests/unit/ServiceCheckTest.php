<?php
/**
 * This file is part of graze/dog-statsd
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/dog-statsd/blob/master/LICENSE.md
 * @link    https://github.com/graze/dog-statsd
 */

namespace Graze\DogStatsD\Test\Unit;

use Graze\DogStatsD\Client;
use Graze\DogStatsD\Test\TestCase;

class ServiceCheckTest extends TestCase
{
    public function testSimpleServiceCheck()
    {
        $this->client->serviceCheck('service.api', Client::STATUS_OK);
        $this->assertEquals('_sc|service.api|0', $this->client->getLastMessage());
    }

    public function testMetaData()
    {
        $this->client->serviceCheck(
            'service.api',
            Client::STATUS_CRITICAL,
            [
                'time'     => 12345678,
                'hostname' => 'some.host',
            ]
        );
        $this->assertEquals(
            '_sc|service.api|2|d:12345678|h:some.host',
            $this->client->getLastMessage()
        );
    }

    public function testTags()
    {
        $this->client->serviceCheck(
            'service.api',
            Client::STATUS_WARNING,
            [
                'time'     => 12345678,
                'hostname' => 'some.host',
            ],
            ['tag']
        );
        $this->assertEquals(
            '_sc|service.api|1|d:12345678|h:some.host|#tag',
            $this->client->getLastMessage()
        );
    }

    public function testMessageIsAfterTags()
    {
        $this->client->serviceCheck(
            'service.api',
            Client::STATUS_UNKNOWN,
            [
                'time'    => 12345678,
                'message' => 'some_message',
            ],
            ['tag']
        );
        $this->assertEquals(
            '_sc|service.api|3|d:12345678|#tag|m:some_message',
            $this->client->getLastMessage()
        );
    }

    public function testCoreStatsDImplementation()
    {
        $this->client->configure([
            'host'    => '127.0.0.1',
            'port'    => 8125,
            'dataDog' => false,
        ]);
        $this->client->serviceCheck('service.api', Client::STATUS_OK);
        $this->assertEquals('', $this->client->getLastMessage());
    }
}
