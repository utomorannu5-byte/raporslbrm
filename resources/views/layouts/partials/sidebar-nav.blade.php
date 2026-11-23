@php
    $user = auth()->user();
    $isAdmin = $user?->hasRole('admin') ?? false;
    $isGuru = $user?->hasRole('guru') ?? false;

    $sections = $isAdmin
        ? [
            [
                'label' => 'Menu Utama',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard'],
                ],
            ],
            [
                'label' => 'Administrasi',
                'items' => [
                    ['label' => 'Pengguna', 'icon' => 'users', 'soon' => true],
                    ['label' => 'Siswa & Kelas', 'icon' => 'students', 'soon' => true],
                    ['label' => 'Tahun Ajaran', 'icon' => 'calendar', 'soon' => true],
                    ['label' => 'Mapel', 'icon' => 'book', 'soon' => true],
                    ['label' => 'Kompetensi', 'icon' => 'checklist', 'soon' => true],
                    ['label' => 'Konversi Nilai', 'icon' => 'sliders', 'soon' => true],
                    ['label' => 'Pengaturan Sekolah', 'icon' => 'school', 'soon' => true],
                    ['label' => 'Arsip Rapor', 'icon' => 'archive', 'soon' => true],
                ],
            ],
        ]
        : ($isGuru
            ? [
                [
                    'label' => 'Menu Utama',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'guru.dashboard'],
                    ],
                ],
                [
                    'label' => 'Operasional',
                    'items' => [
                        ['label' => 'Input Nilai', 'icon' => 'pencil', 'soon' => true],
                        ['label' => 'Cetak Rapor', 'icon' => 'printer', 'soon' => true],
                        ['label' => 'Arsip Rapor', 'icon' => 'folder', 'soon' => true],
                    ],
                ],
            ]
            : []);
@endphp

<nav class="sidebar-nav" aria-label="Menu utama">
    @foreach ($sections as $section)
        <div class="sidebar-section-label">{{ $section['label'] }}</div>

        @foreach ($section['items'] as $item)
            @php
                $isSoon = $item['soon'] ?? false;
                $isActive = ! $isSoon && isset($item['route']) && request()->routeIs($item['route']);
            @endphp

            <a
                href="{{ $isSoon ? '#' : route($item['route']) }}"
                class="nav-link {{ $isActive ? 'active' : '' }} {{ $isSoon ? 'disabled' : '' }}"
                @if ($isSoon)
                    aria-disabled="true"
                    tabindex="-1"
                    onclick="return false;"
                @elseif ($isActive)
                    aria-current="page"
                @endif
            >
                <x-icon :name="$item['icon']" class="nav-icon" />
                <span class="nav-label">{{ $item['label'] }}</span>

                @if ($isSoon)
                    <span class="soon-badge">Soon</span>
                @endif
            </a>
        @endforeach
    @endforeach
</nav>
