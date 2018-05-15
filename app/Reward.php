<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    public static function stockReport()
    {
        $rawData = \DB::table('rewards')
            ->selectRaw('COUNT(*) count, reward_providers.name, SUM(value) total_value, value')
            ->leftJoin('reward_providers', 'rewards.reward_provider_id', '=', 'reward_providers.id')
            ->whereNull('complaint_id')
            ->groupBy('reward_provider_id', 'value')
            ->get();

        // Format the data into something we can use to populate a display table
        $tableData = [
                'totals' => [
                    'grandTotalNumber' => 0,
                    'grandTotalValue' => 0,
                ],
                'types' => [],
            ];

        $valuesArray = array_fill_keys(config('app.reward_values'), 0);

        foreach ($rawData as $voucherType) {
            $tableData['totals']['grandTotalNumber'] += $voucherType->count;
            $tableData['totals']['grandTotalValue'] += $voucherType->total_value;
            if (!isset($tableData['types'][$voucherType->name])) { 
                $tableData['types'][$voucherType->name] = [
                    'number' => $valuesArray,
                    'value' => $valuesArray,
                ];
            }

            if (isset($tableData['types'][$voucherType->name]['number'][$voucherType->value])) {
                $tableData['types'][$voucherType->name]['number'][$voucherType->value] += $voucherType->count;
                $tableData['types'][$voucherType->name]['value'][$voucherType->value] += $voucherType->total_value;
            }
        }

        return $tableData;
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function rewardProvider()
    {
        return $this->belongsTo(RewardProvider::class);
    }
}
