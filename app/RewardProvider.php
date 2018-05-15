<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RewardProvider extends Model
{
    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }
}
