<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
        aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    

    <div class="collapse navbar-collapse" id="navbarColor01">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item {{Request::is('/') ? 'active' : ''}}">
                <a class="nav-link" href="/">Items</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            @if (Route::has('login'))
                @auth
                    <li class="nav-item {{Request::is('dashboard') ? 'active' : ''}}">
                        <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item {{Request::is('upload') ? 'active' : ''}}">
                        <a class="nav-link" href="/upload">Upload</a>
                    </li>
                    <li class="nav-item {{Request::is('logout') ? 'active' : ''}}">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                @else
                    <li class="nav-item {{Request::is('login') ? 'active' : ''}}">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item {{Request::is('register') ? 'active' : ''}}">
                            <a class="nav-link" href="/register">Register</a>
                        </li>
                    @endif
                @endif
            @endif
        </ul>
        {{-- <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search">
            <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
        </form> --}}
    </div>
</nav>