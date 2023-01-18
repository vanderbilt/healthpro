<?php

namespace App\Tests\Entity;

class NphSampleTest extends NphTestCase
{
    /**
     * @dataProvider canUnlockDataProvider
     */

    public function testCanUnlock($sample, $canUnlock)
    {
        $orderData = $this->getOrderData();
        $nphOrder = $this->createNphOrder($orderData);
        $sample['nphOrder'] = $nphOrder;
        $nphSample = $this->createNphSample($sample);
        $this->assertSame($canUnlock, $nphSample->canUnlock());
    }

    public function canUnlockDataProvider(): array
    {
        $finalizedTs = new \DateTime('2023-01-08 08:00:00');
        return [
            [
                [
                    'sampleId' => '1000000000',
                    'sampleCode' => 'SST8P5',
                    'finalizedTs' => $finalizedTs,
                    'modifyType' => 'cancel',
                ],
                false
            ],
            [
                [
                    'sampleId' => '1000000000',
                    'sampleCode' => 'SST8P5',
                    'finalizedTs' => $finalizedTs,
                    'modifyType' => 'unlock',
                ],
                false
            ],
            [
                [
                    'sampleId' => '1000000000',
                    'sampleCode' => 'SST8P5',
                ],
                false
            ],
            [
                [
                    'sampleId' => '1000000000',
                    'sampleCode' => 'SST8P5',
                    'finalizedTs' => $finalizedTs,
                ],
                true
            ]
        ];
    }

    /**
     * @dataProvider aliquotDataProvider
     */
    public function testGetNphAliquotsStatus($aliquotData)
    {
        $sample = $this->createOrderAndSample();
        $aliquotData['nphSample'] = $sample;
        $aliquotData = array_merge($this->getAliquotData(), $aliquotData);
        $this->createNphAliquot($aliquotData);
        $expectedStatus = [
            $aliquotData['aliquotId'] => $aliquotData['status']
        ];
        $this->assertSame($expectedStatus, $sample->getNphAliquotsStatus());
    }

    public function aliquotDataProvider(): array
    {
        return [
            [
                [
                    'status' => 'cancel',
                ]
            ],
            [
                [
                    'status' => 'restore',
                ]
            ],
            [
                [
                    'status' => null,
                ]
            ]
        ];
    }
}