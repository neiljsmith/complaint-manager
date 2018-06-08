<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Reward;

class RewardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStockReport()
    {
        $rewardsData = [
            [
                'reward_provider_id' => 1, // Amazon
                'value' => 5,
                'complaint_id' => null,
            ],
            [
                'reward_provider_id' => 1,
                'value' => 5,
                'complaint_id' => null,
            ],
            [
                'reward_provider_id' => 1,
                'value' => 10,
                'complaint_id' => null,
            ],
            [
                'reward_provider_id' => 2, // Marks and Spencer
                'value' => 15,
                'complaint_id' => null,
            ],
            [
                'reward_provider_id' => 2,
                'value' => 20,
                'complaint_id' => null,
            ],
            // This one will be discounted as complaint_id != null
            [
                'reward_provider_id' => 1,
                'value' => 50,
                'complaint_id' => 1,
            ],
        ];
        $rewards = [];
        foreach ($rewardsData as $rewardData) {
            $rewards[] = factory(Reward::class)->create($rewardData);
        }

        $stockData = Reward::stockReport();

        $this->assertEquals(5, $stockData['totals']['grandTotalNumber']);
        $this->assertEquals(55, $stockData['totals']['grandTotalValue']);
        $this->assertEquals(2, $stockData['types']['Amazon']['number'][5]);
        $this->assertEquals(10, $stockData['types']['Amazon']['value'][5]);
        $this->assertEquals(0, $stockData['types']['Marks and Spencer']['number'][25]);
        $this->assertEquals(15, $stockData['types']['Marks and Spencer']['value'][15]);
    }
}
