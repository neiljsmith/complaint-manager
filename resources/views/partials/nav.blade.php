<?php
use Illuminate\Support\Facades\Auth;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="http://www.neilsmith.com">neilsmith.com</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">

            @auth

            <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">Home</a>
            </li>

                @if (Auth::user()->hasRole([App\Role::ROLE_SUPER_ADMIN]))

            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports') }}">Reports</a>
            </li>

                @endif

                @if (Auth::user()->hasRole([App\Role::ROLE_SUPER_ADMIN, App\Role::ROLE_LINE_MANAGER]))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.index') }}">Users</a>
            </li>

                @endif

            @else

            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">Login</a>
            </li>

            @endauth

        </ul>
        @auth
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}
                </a>

                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a href="{{ route('users.edit', ['user' => Auth::user()]) }}" class="dropdown-item">My Details</a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
        @endauth
    </div>
</nav>