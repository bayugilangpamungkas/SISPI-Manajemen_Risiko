{{-- {{ dd(auth()->user()->id_level) }} --}}
<li class="nav-item">
    <a href="{{ $first_menu->link }}" class="nav-link">
        <i class="nav-icon {{ $first_menu->icon }}"></i>
        <p>
            {{ $first_menu->name }}
        </p>
    </a>
    <hr class="bg-secondary mt-auto mb-auto">
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
                <p>
                    {{ $head_menu->name }}
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @foreach ($head_menu->Menu as $menu)
                    @php
                        $level_menu = $menu->Level_menu->pluck('id_level')->toArray();

                    @endphp

                    @if (in_array(auth()->user()->id_level, $level_menu))
                        <li class="nav-item">
                            <a href="{{ $menu->link }}" class="nav-link">
                                <i class="far {{ $menu->icon }} nav-icon ms-2"></i>
                                <p>
                                    {{ $menu->name }}
                                </p>
                            </a>
                        </li>
                    @endif
                @endforeach

            </ul>
            <hr class="bg-secondary mt-auto mb-auto">
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
    @endphp

    @if ($has_children && count($accessible_children) > 0)
        {{-- Menu with dropdown/children --}}
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon {{ $menu->icon }}"></i>
                <p>
                    {{ $menu->name }}
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="display: none;">
                @foreach ($accessible_children as $child)
                    <li class="nav-item">
                        <a href="{{ $child->link }}" class="nav-link">
                            <i class="{{ $child->icon }} nav-icon ml-3"></i>
                            <p>{{ $child->name }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
            <hr class="bg-secondary mt-auto mb-auto">
        </li>
    @else
        {{-- Regular menu without children --}}
        <li class="nav-item">
            <a href="{{ $menu->link }}" class="nav-link">
                <i class="nav-icon {{ $menu->icon }}"></i>
                <p>
                    {{ $menu->name }}
                </p>
            </a>
            <hr class="bg-secondary mt-auto mb-auto">
        </li>
    @endif
@endforeach

<li class="nav-item">
    <a href="/logout" class="nav-link">
        <i class="nav-icon fa-solid fa-right-from-bracket"></i>
        <p>
            Logout
        </p>
    </a>
    <hr class="bg-secondary mt-auto mb-auto">
</li>
