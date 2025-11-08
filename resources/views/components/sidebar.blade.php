<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">SPI POLINEMA</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">SPI</a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="{{ $first_menu->link }}" class="nav-link">
                    <i class="{{ $first_menu->icon }}"></i>
                    <span>{{ $first_menu->name }}</span>
                </a>
            </li>
            @foreach ($head_menus as $head_menu)
                @php
                    $count = 0;
                @endphp
                @foreach ($head_menu->Menu as $menu)
                    @php
                        $level_menu = $menu->Level_menu->pluck('id_level')->toArray();

                    @endphp

                    @if (in_array(auth()->user()->id_level, $level_menu))
                        @php
                            $count++;
                        @endphp
                    @endif
                @endforeach
                @if ($count > 0)
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon {{ $head_menu->icon }}"></i>
                            <span>{{ $head_menu->name }}</span>
                            <i class="fas fa-angle-left right"></i>
                        </a>
                        @foreach ($head_menu->Menu as $menu)
                            @php
                                $level_menu = $menu->Level_menu->pluck('id_level')->toArray();

                            @endphp

                            @if (in_array(auth()->user()->id_level, $level_menu))
                                <?php
                                $active = ltrim($menu->link, '/');
                                ?>

                    <li class="{{ Request::is($active) ? 'active' : '' }}">
                        <a href="{{ $menu->link }}" class="nav-link">
                            <i class="{{ $menu->icon }}"></i>
                            <span>{{ $menu->name }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
            </li>
            @endif
            @endforeach

            @foreach ($panel_menus as $menu)
                <li class="{{ $active == $menu->id ? 'active' : '' }}">
                    <a href="{{ $menu->link }}" class="nav-link">
                        <i class="nav-icon {{ $menu->icon }}"></i>
                        <span>{{ $menu->name }}</span>
                    </a>
                </li>
            @endforeach

            <li>
                <a href="/logout" class="nav-link">
                    <i class="fas fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>
</div>
