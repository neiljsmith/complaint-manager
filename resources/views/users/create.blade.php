@extends('layouts.app') 
@section('content')
<div class="container">
    <div class="row justify-content-center" id="users-create">
        <div class="col-md-8">
            <h1>Create User</h1>
            
            <form id="users-create-form" action="/users" method="POST" data-validate="users.create">

                @csrf

                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name">First Name</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" data-val-rule="required" name="first_name" id="first_name" value="{{ old('first_name') }}" />
                        
                    <div class="{{ $errors->has('first_name') ? '' : 'd-none' }} text-danger">First Name is required</div>
                </div>
    
                <!-- Last Name -->
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" data-val-rule="required" name="last_name" id="last_name" value="{{ old('last_name') }}" />
                        
                    <div class="{{ $errors->has('last_name') ? '' : 'd-none' }} text-danger">Last Name is required</div>
                </div>
        
                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" data-val-rule="required|email|emailExists" name="email" id="email" value="{{ old('email') }}" />

                    @if ($errors->has('email'))
                    <div class="text-danger" data-val-msg="required">{{ $errors->first('email') }}</div>
                    @endif

                    <div class="d-none text-danger" data-val-msg="required">Email is required</div>
                    <div class="d-none text-danger" data-val-msg="email">Please enter a valid email</div>
                    <div class="d-none text-danger" data-val-msg="emailExists">This email address is already in use</div>
                </div>
        
                <!-- Role -->
                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select class="form-control {{ $errors->has('role_id') ? 'is-invalid' : '' }}" data-val-rule="roleId" name="role_id" id="role_id" >
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}"{{ $role->id == old('role_id') ? ' selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>

                    <div class="d-none text-danger" data-val-msg="roleId"></div>
                </div>

                <!-- Line Manager -->
                <div class="form-group">
                    <label for="line_manager_user_id">Line Manager</label>
                    <select class="form-control {{ $errors->has('line_manager_user_id') ? 'is-invalid' : '' }}" data-val-rule="lineManagerId" name="line_manager_user_id" id="line_manager_user_id">
                        <option value="0">N/A</option>
                        @foreach ($managers as $manager)
                        <option value="{{ $manager->id }}"{{ $manager->id == old('line_manager_user_id', $managers[0]->id) ? ' selected' : '' }}>{{ $manager->first_name . ' ' . $manager->last_name }}</option>
                        @endforeach
                    </select>
                            
                    @if ($errors->has('line_manager_user_id'))
                    <div class="text-danger" data-val-msg="required">{{ $errors->first('line_manager_user_id') }}</div>
                    @endif

                    <div class="d-none text-danger" data-val-msg="lineManagerId"></div>
                </div>    
                    
                <!-- Active -->
                <input type="hidden" name="active" id="active" value="1">

                <input type="hidden" name="id" id="id" value="0">
                <input type="hidden" name="has-subordinates" id="has-subordinates", value="0">
                <div class="form-group">
                <button class="btn btn-primary" type="submit">Save</button> <a class="btn btn-secondary" href="{{ url()->previous() }}">Cancel</a>
                </div>

            </form>

        </div><!-- /.col-md-8 -->
    </div>
</div>
@endsection