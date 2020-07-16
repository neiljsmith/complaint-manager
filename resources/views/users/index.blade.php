@extends('layouts.app') 
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h1>Users</h1>
        </div>
        <div class="col-md-4">
            <a href="{{ route('users.create') }}" class="btn btn-primary float-right">Create New User</a>
        </div>
        </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('partials.user-table')
            <div class="ml-auto">
            {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection