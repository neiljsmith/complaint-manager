@extends('layouts.app') 
@section('content')
<div class="container">
    <div class="row justify-content-center" id="users-edit">
        <div class="col-md-8">
            @if (Auth::user()->id === $user->id)
            <h1>My Details</h1>
            @else
            <h1>Edit User</h1>
            @endif
            
            <form id="users-edit-form" action="/users/{{ $user->id }}" method="POST" data-validate="users.edit">

                @csrf
                @method('PATCH')

                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name">First Name</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" data-val-rule="required" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" />
                        
                    <div class="{{ $errors->has('first_name') ? '' : 'd-none' }} text-danger">First Name is required</div>
                </div>
    
                <!-- Last Name -->
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" data-val-rule="required" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" />
                        
                    <div class="{{ $errors->has('last_name') ? '' : 'd-none' }} text-danger">Last Name is required</div>
                </div>
        
                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" data-val-rule="required|email|emailExists" name="email" id="email" value="{{ old('email', $user->email) }}" />

                    @if ($errors->has('email'))
                    <div class="text-danger" data-val-msg="required">{{ $errors->first('email') }}</div>
                    @endif

                    <div class="d-none text-danger" data-val-msg="required">Email is required</div>
                    <div class="d-none text-danger" data-val-msg="email">Please enter a valid email</div>
                    <div class="d-none text-danger" data-val-msg="emailExists">This email address is already in use</div>
                </div>
        
                @if (Auth::user()->id !== $user->id)

                <!-- Role -->
                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select class="form-control {{ $errors->has('role_id') ? 'is-invalid' : '' }}" data-val-rule="roleId" name="role_id" id="role_id" >
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}"{{ $role->id == old('role_id', $user->roles[0]->id) ? ' selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                            
                    <div class="{{ $errors->has('role_id') ? '' : 'd-none' }} text-danger">An Agent may not manage other staff - see list below</div>
                </div>

                <!-- Line Manager -->
                <div class="form-group">
                    <label for="line_manager_user_id">Line Manager</label>
                    <select class="form-control {{ $errors->has('line_manager_user_id') ? 'is-invalid' : '' }}" data-val-rule="lineManagerId" name="line_manager_user_id" id="line_manager_user_id">
                        <option value="0">N/A</option>
                        @foreach ($managers as $manager)
                        <option value="{{ $manager->id }}"{{ $manager->id == old('line_manager_user_id', $user->line_manager_user_id) ? ' selected' : '' }}>{{ $manager->first_name . ' ' . $manager->last_name }}</option>
                        @endforeach
                    </select>
                            
                    @if ($errors->has('line_manager_user_id'))
                    <div class="text-danger" data-val-msg="required">{{ $errors->first('line_manager_user_id') }}</div>
                    @endif

                    <div class="d-none text-danger" data-val-msg="lineManagerId"></div>
                </div>    
                    
                <!-- Active -->
                <div class="form-check">
                    <input type="checkbox" data-val-rule="activeHasSubordinates|activeTooFewSuperAdmins" class="form-check-input {{ $errors->has('active') ? 'is-invalid' : '' }}" name="active" id="active" value="1" {{ old('active', $user->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>

                    @if ($errors->has('active'))
                    <div class="text-danger" data-val-msg="required">{{ $errors->first('active') }}</div>
                    @endif

                    <div class="d-none text-danger" data-val-msg="activeHasSubordinates">A Manager with staff may not be deactivated</div>
                    <div class="d-none text-danger" data-val-msg="activeTooFewSuperAdmins">There must be a minimum of two Super Admins</div>
                </div>

                @else

                <input type="hidden" name="role_id"" id="role_id"" value={{ $user->roles[0]->id }}>
                <input type="hidden" name="line_manager_user_id"" id="line_manager_user_id"" value={{ $user->line_manager_user_id }}>
                <input type="hidden" name="active" id="active" value="1">

                @endif

                <input type="hidden" name="id" id="id" value={{ $user->id }}>
                <input type="hidden" name="has-subordinates" id="has-subordinates", value="{{ count($users) ? '1' : '0' }}">
                <input type="hidden" name="num-super-admins" id="num-super-admins" value="{{ $numSuperAdmins }}">

                <div class="form-group">
                <button class="btn btn-primary" type="submit">Save</button> <a class="btn btn-secondary" href="{{ url()->previous() }}">Cancel</a>
                </div>

            </form>

            @if (count($users))
            <hr>
            <h4>Staff managed by this user</h2>
            @include('partials.user-table')
            @endif
            
        </div><!-- /.col-md-8 -->
    </div>
</div>
@endsection