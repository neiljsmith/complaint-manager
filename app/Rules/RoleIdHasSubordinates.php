<?php

namespace App\Rules;

use App\Role;
use App\User;
use Illuminate\Contracts\Validation\Rule;

class RoleIdHasSubordinates implements Rule
{
    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->user = User::find($userId);
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
        if (!$this->user) {
            // Creating a new user so this rule doesn't apply
            return true;
        }

        $hasSubordinates = $this->user->subordinates()->count() > 0 ? true : false;
        $AgentRoleId = Role::where('name', Role::ROLE_AGENT)->pluck('id')->first();

        if ($value > $AgentRoleId) {
            return true;
        } elseif ($value == $AgentRoleId && $hasSubordinates === false) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'An Agent may not manage other staff - see list below';
    }
}
