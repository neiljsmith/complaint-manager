<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\User;
use App\Role;

class LineManagerUserId implements Rule
{
    private $user;

    private $role;

    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId, $roleId)
    {
        $this->user = User::find($userId);
        $this->role = Role::find($roleId);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $lineManagerSelected = $value == 0 ? false : true;
        $agentRank = Role::where('name', Role::ROLE_AGENT)->pluck('rank')->first();
        if ($lineManagerSelected && $this->role->rank > $agentRank) {
            $this->message = 'A Manager may not have a Line Manager';
            return false;
        } 
        if (!$lineManagerSelected && $this->role->rank === $agentRank) {
            $this->message = 'An Agent must have a Line Manager';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
