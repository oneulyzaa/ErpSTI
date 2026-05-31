
@foreach ($menuItems as $item)

    {{-- ── JENIS: single ──────────────────────────────────────────── --}}
    @if ($item['type'] === 'single')
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                   class="nav-link {{ $item['route'] && Request::routeIs($item['route']) ? 'active' : '' }}"
                   id="{{ $item['id'] }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        </ul>

    {{-- ── JENIS: group (label bagian) ───────────────────────────── --}}
    @elseif ($item['type'] === 'group')
        <div class="sidebar-section sidebar-label">{{ $item['label'] }}</div>

    {{-- ── JENIS: submenu (collapsible) ──────────────────────────── --}}
    @elseif ($item['type'] === 'submenu')
        @php
            $isOpen = Request::routeIs(...$item['routes']);
        @endphp
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a class="nav-link {{ $isOpen ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   href="#{{ $item['submenu_id'] }}"
                   role="button"
                   aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                   id="{{ $item['id'] }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                    <i class="bi bi-chevron-right collapse-arrow"></i>
                </a>
                <div class="collapse submenu {{ $isOpen ? 'show' : '' }}"
                     id="{{ $item['submenu_id'] }}">
                    <ul class="nav flex-column gap-1 mt-1">
                        @foreach ($item['children'] as $child)
                            <li>
                                <a href="{{ $child['route'] ? route($child['route']) : '#' }}"
                                   class="nav-link {{ $child['route'] && Request::routeIs($child['route']) ? 'active' : '' }}"
                                   id="{{ $child['id'] }}">
                                    <i class="{{ $child['icon'] }}"></i>
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>

    @endif

@endforeach

{{-- Logout (mobile only, desktop ada di footer sidebar) --}}
<div class="d-lg-none border-top border-secondary border-opacity-25 mt-3 pt-3">
    <ul class="nav flex-column">
        <li>
            <a href="{{ route('logout') }}" class="nav-link text-danger" id="menu-logout-mobile">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</div>