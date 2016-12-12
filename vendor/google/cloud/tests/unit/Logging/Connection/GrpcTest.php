<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Tests\Logging\Connection;

use Google\Cloud\Logging\Connection\Grpc;
use Google\Cloud\GrpcRequestWrapper;
use Google\Cloud\PhpArray;
use Prophecy\Argument;
use google\logging\v2\LogEntry;
use google\logging\v2\LogMetric;
use google\logging\v2\LogSink;

/**
 * @group logging
 */
class GrpcTest extends \PHPUnit_Framework_TestCase
{
    private $requestWrapper;
    private $successMessage;

    public function setUp()
    {
        if (!extension_loaded('grpc')) {
            $this->markTestSkipped('Must have the grpc extension installed to run this test.');
        }

        $this->requestWrapper = $this->prophesize(GrpcRequestWrapper::class);
        $this->successMessage = 'success';
    }

    /**
     * @dataProvider methodProvider
     */
    public function testCallBasicMethods($method, $args, $expectedArgs)
    {
        $this->requestWrapper->send(
            Argument::type('callable'),
            $expectedArgs,
            Argument::type('array')
        )->willReturn($this->successMessage);

        $grpc = new Grpc();
        $grpc->setRequestWrapper($this->requestWrapper->reveal());

        $this->assertEquals($this->successMessage, $grpc->$method($args));
    }

    public function methodProvider()
    {
        $value = 'value';
        $entryData = [
            'logName' => $value,
            'resource' => [
                'type' => $value,
                'labels' => [
                    [
                        'key' => $value,
                        'value' => $value
                    ]
                ]
            ],
            'jsonPayload' => [
                'fields' => [
                    'key' => $value,
                    'value' => [
                        'string_value' => $value
                    ]
                ]
            ],
            'labels' => [
                [
                    'key' => $value,
                    'value' => $value
                ]
            ]
        ];
        $sinkData = [
            'name' => $value,
            'destination' => $value,
            'filter' => $value,
            'outputVersionFormat' => 'V2'
        ];
        $metricData = [
            'name' => $value,
            'description' => $value,
            'filter' => $value
        ];
        $pbEntry = (new LogEntry())->deserialize($entryData, new PhpArray());
        $pbSink = (new LogSink())->deserialize(['outputVersionFormat' => 1] + $sinkData, new PhpArray());
        $pbMetric = (new LogMetric())->deserialize($metricData, new PhpArray());
        $resourceNames = ['projects/id'];
        $pageSizeSetting = ['pageSize' => 2];

        return [
            [
                'writeEntries',
                [
                    'entries' => [
                        [
                            'logName' => $value,
                            'resource' => [
                                'type' => $value,
                                'labels' => [
                                    $value => $value
                                ]
                            ],
                            'jsonPayload' => [
                                $value => $value
                            ],
                            'labels' => [
                                $value => $value
                            ]
                        ]
                    ]
                ],
                [[$pbEntry], []]
            ],
            [
                'listEntries',
                ['resourceNames' => $resourceNames],
                [$resourceNames, []]
            ],
            [
                'createSink',
                ['parent' => $value] + $sinkData,
                [$value, $pbSink, []]
            ],
            [
                'getSink',
                ['sinkName' => $value],
                [$value, []]
            ],
            [
                'listSinks',
                ['parent' => $value] + $pageSizeSetting,
                [$value, $pageSizeSetting]
            ],
            [
                'updateSink',
                ['sinkName' => $value] + $sinkData,
                [$value, $pbSink, []]
            ],
            [
                'deleteSink',
                ['sinkName' => $value],
                [$value, []]
            ],
            [
                'createMetric',
                ['parent' => $value] + $metricData,
                [$value, $pbMetric, []]
            ],
            [
                'getMetric',
                ['metricName' => $value],
                [$value, []]
            ],
            [
                'listMetrics',
                ['parent' => $value] + $pageSizeSetting,
                [$value, $pageSizeSetting]
            ],
            [
                'updateMetric',
                ['metricName' => $value] + $metricData,
                [$value, $pbMetric, []]
            ],
            [
                'deleteMetric',
                ['metricName' => $value],
                [$value, []]
            ],
            [
                'deleteLog',
                ['logName' => $value],
                [$value, []]
            ]
        ];
    }
}
