<?php

namespace App;

use App\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'line_manager_user_id', 'password', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function authorizeRoles($roles)
    {
        // Make sure we're dealing with an array if $roles is a scalar
        $rolesArray = is_array($roles) ? $roles : [$roles];

        // Check if
        if (!$this->roles()->whereIn('name', $rolesArray)->exists()) {
            //abort(401, 'This action is unauthorized.');
            return redirect('/')->with('status', 'You are not authorized!');
        }
    }

    /**
     * Use to seed `users` and `role_user` table
     *
     * @param integer $total
     * @return void
     */
    public static function factory($total = 1)
    {
        for ($i = 0; $i < $total; $i++) {
            $userId = factory(static::class)->create()->id;
            DB::statement('INSERT INTO `role_user` (role_id, user_id) VALUES (1, ' . $userId . ')');
        }
    }

    /**
     * Many-to-many relationship with 'roles', although
     * for our purposes it will effectively be many-to-one
     *
     * @return void
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * One-to-many relationship with other 'users' if a
     * user is a Line Manager or Super Admin
     *
     * @return void
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'line_manager_user_id');
    }

    /**
     * Many-to-one relationship with another 'user' if a
     * user has a line manager
     *
     * @return void
     */
    public function lineManager()
    {
        return $this->belongsTo(User::class, 'line_manager_user_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function complaintNotes()
    {
        return $this->hasMany(ComplaintNote::class);
    }

}
