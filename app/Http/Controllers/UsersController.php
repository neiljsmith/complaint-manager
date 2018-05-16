<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Mail\Welcome;
use Illuminate\Http\Request;
use App\Http\Requests\UserCreateUpdate;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class UsersController extends Controller
{
    use ResetsPasswords;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['test']]);
        $this->middleware('checkRoles:'. Role::ROLE_SUPER_ADMIN . ',' . Role::ROLE_LINE_MANAGER);
    }

    /**
     * Display paginated users list
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $itemsPerPage = 10;
        $users = User::with('roles')
            ->orderBy('last_name')
            ->paginate($itemsPerPage);

        return view('users.index', compact('users'));
    }

    /**
     * Returns array of data rendered as JSON for AJAX calls
     *
     * @param User $user
     * @return array 
     */
    public function show(User $user) 
    {
        // Also load role info from linked table
        $user->load('roles');

        // Load all roles for selection in the form
        $roles = Role::all();

        // Get eligible line managers
        $managers = $this->managers();

        // Users managed by this user
        $users = $user->subordinates()->get();

        return compact('user', 'roles', 'managers', 'users');
    }

    /**
     * Displays user creation form
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Load all roles for selection in the form
        $roles = Role::all();

        // Get eligible line managers
        $managers = $this->managers();

        return view('users.create', compact('roles', 'managers'));
    }

    /**
     * Displays user edit form
     *
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Also load role info from linked table
        $user->load('roles');

        // Load all roles for selection in the form
        $roles = Role::all();

        // Get eligible line managers
        $managers = $this->managers();

        // Users managed by this user
        $users = $user->subordinates()->orderBy('last_name')->get();

        // Number of Super Admins, required so we never have less than two in total
        $numSuperAdmins = User::whereHas('roles', function($query) {
            $query->where('name', Role::ROLE_SUPER_ADMIN);
        })->count();

        return view('users.edit', compact('user', 'roles', 'managers', 'users', 'numSuperAdmins'));
    }

    /**
     * Validates the request, creates user and sends welcome email
     * with a password reset link.
     * Type hinting our own request class means validation from that class
     * will be applied automatically.
     *
     * @param UserCreateUpdate $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserCreateUpdate $request)
    {
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'line_manager_user_id' => $request->input('line_manager_user_id'),
            'password' => \Hash::make(str_random(35)),
            'remember_token' => str_random(10),
        ]);
        $user->roles()->attach($request->input('role_id'));

        // Generate a new reset password token
        // A hashed version will be saved in password_resets table
        $token = Password::broker()->createToken($user);

        // Note use of \Mail facade
        \Mail::to($user)->send(new Welcome($user, $token));

        // Create a flash message that will only be available for 
        // a single page view. Will be displayed in the app.blade.php view.
        session()->flash('message', 'User created successfully!');

        return redirect()->route('users.index');
    }

    /**
     * Update user and redirect to users index
     *
     * @param UserCreateUpdate $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserCreateUpdate $request, User $user)
    {
        // Account for unchecked 'active' checkbox
        $user->active = $request->input('active') ? 1 : 0;

        // As we're only having one role per user, detach any
        // previous value before attaching the new one
        $user->roles()->detach($user->roles[0]->id);
        $user->roles()->attach($request->role_id);

        $user->update($request->all());

        return redirect()->route('users.index');
    }

    /**
     * Get eligible line managers, who must have a rank greater than 
     * 'Agent' and be active
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function managers()
    {
        // Get rank > Agent
        $agentRank = Role::where('name', Role::ROLE_AGENT)->pluck('rank')->first();

        return User::whereHas('roles', function($query) use ($agentRank) {
            $query->where('rank', '>', $agentRank)
                ->where('active', 1);
        })->orderBy('last_name')->get();
    }

    /**
     * Gets the total number of Super Admins
     *
     * @return int
     */
    public function numSuperAdmins()
    {
        return User::whereHas('roles', function($query) {
            $query->where('name', Role::ROLE_SUPER_ADMIN);
        })->count();
    }

    /**
     * Returns true (1) if user has subordinates, false (0) if not
     *
     * @param User $user
     * @return int
     */
    public function hasSubordinates(User $user)
    {
        return $user->subordinates()->count() > 0 ? 1 : 0;
    }

    /**
     * Finds if email exists in the DB, not assigned to the User
     * whose ID is passed in. Using $id instead of User instance
     * as param in case 0 is passed, i.e. for new user.
     *
     * @param  $id
     * @param Request $request
     * @return int number of dupes found, always 1 or 0
     */
    public function duplicateEmail($id, $email)
    {
        return User::where('id', '!=', $id)
            ->where('email', strtolower(trim($email)))
            ->count();
    }
}
