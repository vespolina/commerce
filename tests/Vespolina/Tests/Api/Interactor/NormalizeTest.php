<?php

namespace Vespolina\Tests\Api\Interactor;

use Vespolina\Api\Interactor\Normalize;

class NormalizeTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizePrices()
    {
        $data = [
            '_id' => 234,
            'prices' => [
                0 => [
                    'type' => 'total',
                    'value' => 123,
                ],
                1 => [
                    'type' => 'taxes',
                    'value' => 12.3,
                ],
            ],
            'items' => [
                0 => [
                    'name' => 'Name',
                    'prices' => [
                        0 => [
                            'type' => 'unit',
                            'value' => 333,
                        ],
                        1 => [
                            'type' => 'wholesale',
                            'value' => 275,
                        ],
                    ],
                    'quantity' => 1,
                    'product' => [
                        'name' => 'Name',
                        'prices' => [
                            0 => [
                                'type' => 'unit',
                                'value' => 333,
                            ],
                            1 => [
                                'type' => 'wholesale',
                                'value' => 275,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expected = [
            '_id' => 234,
            'prices' => [
                'total' => 123,
                'taxes' => 12.3,
            ],
            'items' => [
                0 => [
                    'name' => 'Name',
                    'prices' => [
                        'unit' => 333,
                        'wholesale' => 275,
                    ],
                    'quantity' => 1,
                    'product' => [
                        'name' => 'Name',
                        'prices' => [
                            'unit' => 333,
                            'wholesale' => 275,
                        ],
                    ],
                ],
            ],
        ];

        $normalized = $this->createNormalize()->process($data);

        $this->assertSame($expected, $normalized);
    }

    protected function createNormalize()
    {
        return new Normalize();
    }
}
 