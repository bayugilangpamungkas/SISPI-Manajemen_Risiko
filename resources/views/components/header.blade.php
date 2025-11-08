<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user d-flex align-items-center">
                @if (auth()->user()->profile_picture)
                    <img alt="image" src="/profile_pictures/{{ auth()->user()->profile_picture }}" class="rounded-circle mr-2" style="width: 40px; height: 40px;">
                @else
                    <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-2" style="width: 40px; height: 40px;">
                @endif
                <div class="d-inline-block" style="line-height: 1;">
                    <span style="font-weight: bold; font-size: 1rem;">{{ auth()->user()->name }}</span><br>
                    <span style="font-size: 0.75rem;">
                        @switch(auth()->user()->id_level)
                            @case(1)
                                Super Administrator
                            @break
        
                            @case(2)
                                Administrator
                            @break
        
                            @case(3)
                                Ketua
                            @break
        
                            @case(4)
                                Anggota
                            @break
        
                            @case(5)
                                Auditee
                            @break
        
                            @case(6)
                                Sekretaris
                            @break
        
                            @default
                                Unknown
                        @endswitch
                    </span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profileDataUser', Auth::user()->id) }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>
                <a href="{{ route('manualbook') }}" class="dropdown-item has-icon">
                    <i class="fas fa-book"></i> Manualbook & Peraturan
                </a>
                <a href="/feedback" class="dropdown-item has-icon">
                    <i class="fas fa-comment"></i> Feedback
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </li>
        
    </ul>
</nav>
