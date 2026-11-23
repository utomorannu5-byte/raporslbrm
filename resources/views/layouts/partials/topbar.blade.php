@php
    $user = auth()->user();
    $roleName = $user?->getRoleNames()->first();
    $roleLabel = match ($roleName) {
        'admin' => 'Administrator',
        'guru' => 'Guru / Wali Kelas',
        default => 'Pengguna',
    };
    $initial = mb_strtoupper(mb_substr($user?->name ?? 'U', 0, 1));
@endphp

<header class="app-topbar">
    <div class="topbar-inner">
        <button
            class="btn btn-light border d-lg-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebarOffcanvas"
            aria-controls="sidebarOffcanvas"
            aria-label="Buka menu"
        >
            <x-icon name="menu" class="d-block" style="width: 1.15rem; height: 1.15rem;" />
        </button>

        <div class="min-w-0">
            <div class="topbar-eyebrow d-none d-sm-block">SLB Rumah Melati</div>
            <h1 class="topbar-title">{{ $title ?? 'Dashboard' }}</h1>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2 gap-sm-3">
            <span class="badge rounded-pill role-badge d-none d-md-inline-flex">{{ $roleLabel }}</span>

            <a href="{{ route('profile.edit') }}" class="user-chip d-none d-sm-flex">
                <span class="avatar-circle">{{ $initial }}</span>
                <span class="user-meta d-none d-md-block">
                    <span class="user-name d-block">{{ $user?->name }}</span>
                    <span class="user-role">{{ $roleName ? ucfirst($roleName) : 'Tanpa role' }}</span>
                </span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-2" title="Keluar dari aplikasi">
                    <x-icon name="logout" style="width: 1rem; height: 1rem;" />
                    <span class="d-none d-md-inline">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</header>
