@extends('layouts.app') 
@section('content')
<div class="container">
    <div class="row justify-content-center" id="users-edit">
        <div class="col-md-8">
            <h1>Edit User</h1>

            <hr>
            <vue-form id="users-edit-form" :state="formstate" @submit.prevent="onSubmit" action="/users" method="POST">

                @csrf
                @method('PATCH')

                <!-- First Name -->
                <validate auto-label class="form-group required-field" :class="fieldClassName(formstate.first_name)">
                    <label for="first_name">First Name</label>
                    <input class="form-control" v-model.lazy="model.first_name" name="first_name" id="first_name" required value="" />
                        
                    <field-messages name="first_name" show="$touched || $submitted" class="form-control-feedback">
                        <div class="text-danger" slot="required">First Name is a required field</div>
                    </field-messages>
                </validate>
    
                <!-- Last Name -->
                <validate auto-label class="form-group required-field" :class="fieldClassName(formstate.last_name)">
                    <label for="last_name">Last Name</label>
                    <input class="form-control" v-model.lazy="model.last_name" name="last_name" id="last_name" required value="{{ $user->last_name }}" />
                        
                    <field-messages name="last_name" show="$touched || $submitted" class="form-control-feedback">
                        <div class="text-danger" slot="required">Last Name is a required field</div>
                    </field-messages>
                </validate>
        
                <!-- Email -->
                <validate auto-label class="form-group required-field" :class="fieldClassName(formstate.email)">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" v-model.lazy="model.email" name="email" id="email" required value="{{ $user->email }}" />
                        
                    <field-messages name="email" show="$touched || $submitted" class="form-control-feedback">
                        <div class="text-danger" slot="required">Email is a required field</div>
                        <div class="text-danger" slot="email">Email is not valid</div>
                    </field-messages>
                </validate>
        
                <!-- Role -->
                <validate auto-label class="form-group required-field" :class="fieldClassName(formstate.role_id)">
                    <label for="role_id">Role</label>
                    <select class="form-control" name="role_id" id="role_id" v-model.lazy="model.role_id" role-validator>
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}"{{ $role->id === $user->roles[0]->id ? ' selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                            
                    <field-messages name="role_id" show="$touched || $dirty || $submitted" class="form-control-feedback">
                        <div class="text-danger" slot="role-validator">Role error text TODO</div>
                    </field-messages>
                </validate>

                <!-- Line Manager -->
                <validate auto-label class="form-group required-field" :class="fieldClassName(formstate.line_manager_user_id)">
                    <label for="role_id">Line Manager</label>
                    <select class="form-control" name="line_manager_user_id" id="line_manager_user_id" v-model.lazy="model.line_manager_user_id" line-manager-validator>
                        <option value="0">N/A</option>
                        @foreach ($superiors as $superior)
                        <option value="{{ $superior->id }}"{{ $superior->id === $user->line_manager_user_id ? ' selected' : '' }}>{{ $superior->first_name . ' ' . $superior->last_name }}</option>
                        @endforeach
                    </select>
                            
                    <field-messages name="line_manager_user_id" show="$touched || $dirty || $submitted" class="form-control-feedback">
                        <div class="text-danger" slot="line-manager-validator">Line Manager error text TODO</div>
                    </field-messages>
                </validate>    
                    
                <!-- Active -->
                <validate class="form-group form-check" :class="fieldClassName('formstate.active')">
                    <input type="checkbox" class="form-check-input" v-model.lazy="model.active" name="active" id="active" value="1"{{ $user->active ? ' checked' : '' }} active-validator>
                    <label class="form-check-label" for="active">Active</label>

                    <field-messages name="active" show="$touched || $dirty || $submitted" class="form-control-feedback">
                        <div class="text-danger" slot="active-validator">Active error text TODO</div>
                    </field-messages>
                </validate>

                <input type="hidden" name="id" id="id" value={{ $user->id }}>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit" name="submit" id="submit">Save</button>
                </div>

            </vue-form>

            @if (count($users))
            <hr>
            <h4>Staff managed by this user</h2>
            @include('partials.user-table')
            @endif
            
        </div><!-- /.col-md-8 -->
    </div>
</div>
@endsection