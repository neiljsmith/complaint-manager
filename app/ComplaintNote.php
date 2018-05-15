<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComplaintNote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'user_id'
    ];

    public function complaints()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
