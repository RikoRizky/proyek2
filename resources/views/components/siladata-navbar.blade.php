@props([
    'id' => null,
])

<nav {{ $id ? 'id='.$id : '' }} class="siladata-floating-nav">
    <div class="siladata-nav-left">
        <a href="{{ url('/') }}"
           onclick="if(window.location.pathname === '/' || window.location.pathname === ''){ event.preventDefault(); window.scrollTo({top: 0, behavior: 'smooth'}); history.pushState(null, '', '/'); }"
           class="siladata-nav-brand-wrap"
           title="SILADATA - Beranda">
            <div class="siladata-nav-brand-main">
                <img src="{{ asset('images/logo-new.png') }}?v={{ file_exists(public_path('images/logo-new.png')) ? filemtime(public_path('images/logo-new.png')) : time() }}"
                     alt="SILADATA"
                     class="siladata-nav-logo"
                     style="pointer-events: none;" />
                <span class="siladata-nav-brand">SILADATA</span>
            </div>
            <div class="siladata-nav-divider"></div>
            <span class="siladata-nav-subtitle">Sistem Layanan Dokumen Akreditasi</span>
        </a>
    </div>

    <div class="siladata-nav-right">
        <a href="{{ route('harga') }}" class="siladata-nav-link">Harga</a>
        @auth
            <a href="{{ route('dashboard') }}" class="siladata-nav-btn">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="siladata-nav-btn">Masuk</a>
        @endauth
    </div>
</nav>
