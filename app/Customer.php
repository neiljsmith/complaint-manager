<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
