<div class="d-flex flex-column h-100">

    <!-- MENÚ -->
    <ul class="nav flex-column">
        @foreach ($menu as $item)
            <li class="nav-item">
                <a href="{{ route($item['route']) }}"
                   class="nav-link d-flex align-items-center 
                   {{ request()->routeIs($item['route']) ? 'active' : '' }}">

                    <i class="bi {{ $item['icon'] }}"></i>

                    <span class="ms-2 text-label">
                        {{ $item['name'] }}
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    <!-- USUARIO ABAJO -->
    <div class="mt-auto">
        <hr class="text-secondary">

        <strong>{{ auth()->user()->name ?? 'Usuario' }}</strong><br>
        <small>{{ auth()->user()->email ?? '' }}</small>
    </div>

</div>