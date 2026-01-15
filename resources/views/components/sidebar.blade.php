ia<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">SPI POLINEMA</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">SPI</a>
        </div>
        <ul class="sidebar-menu">
            @php
                $currentPanelActive = $active ?? null;
            @endphp
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
                                @php
                                    $menuPath = ltrim($menu->link, '/');
                                @endphp

                    <li class="{{ Request::is($menuPath) ? 'active' : '' }}">
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
                @php
                    $has_children = $menu->children->count() > 0;
                    $accessible_children = [];

                    // Check if user has access to any children
                    if ($has_children) {
                        foreach ($menu->children as $child) {
                            $child_level_menu = $child->Level_menu->pluck('id_level')->toArray();
                            if (in_array(auth()->user()->id_level, $child_level_menu)) {
                                $accessible_children[] = $child;
                            }
                        }
                    }
                    $menuPath = ltrim($menu->link, '/');
                @endphp

                @if ($has_children && count($accessible_children) > 0)
                    {{-- Menu with dropdown/children --}}
                    <li class="dropdown">
                        <a href="{{ $menu->link }}" class="nav-link has-dropdown">
                            <i class="{{ $menu->icon }}"></i>
                            <span class="menu-text">{{ $menu->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach ($accessible_children as $child)
                                @php
                                    $childPath = ltrim($child->link, '/');
                                @endphp
                                <li class="{{ Request::is($childPath) ? 'active' : '' }}">
                                    <a href="{{ $child->link }}" class="nav-link">
                                        <i class="{{ $child->icon }}"></i>
                                        <span>{{ $child->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    {{-- Regular menu without children --}}
                    <li class="{{ Request::is($menuPath) || $currentPanelActive == $menu->id ? 'active' : '' }}">
                        <a href="{{ $menu->link }}" class="nav-link">
                            <i class="{{ $menu->icon }}"></i>
                            <span>{{ $menu->name }}</span>
                        </a>
                    </li>
                @endif
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
