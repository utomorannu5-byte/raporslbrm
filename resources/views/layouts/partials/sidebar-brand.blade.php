@php
    $isOffcanvas = $offcanvas ?? false;
@endphp

<div class="sidebar-brand">
    <div class="brand-mark">RM</div>
    <div class="min-w-0">
        <p class="brand-title">E-Rapor SLB</p>
        <p class="brand-subtitle">Rumah Melati</p>
    </div>

    @if ($isOffcanvas)
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="offcanvas" aria-label="Tutup menu"></button>
    @endif
</div>
