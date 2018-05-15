<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ROLE_AGENT = 'Agent';
    const ROLE_LINE_MANAGER = 'Line Manager';
    const ROLE_SUPER_ADMIN = 'Super Admin';

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
