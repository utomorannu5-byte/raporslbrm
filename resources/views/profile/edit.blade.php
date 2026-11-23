<x-app-layout title="Profil Saya">
    <div class="page-heading">
        <div>
            <h1>Profil Saya</h1>
            <p>Kelola informasi akun dan keamanan akses Anda.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="surface-card">
                <div class="surface-card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="surface-card mt-4">
                <div class="surface-card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="surface-card">
                <div class="surface-card-body">
                    <div class="card-heading">
                        <div>
                            <h2>Akun</h2>
                            <p>Ringkasan akses akun Anda.</p>
                        </div>
                    </div>

                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-semibold">Nama</dt>
                        <dd class="col-7 mb-2">{{ $user->name }}</dd>

                        <dt class="col-5 text-muted fw-semibold">Email</dt>
                        <dd class="col-7 mb-2 text-break">{{ $user->email }}</dd>

                        <dt class="col-5 text-muted fw-semibold">Role</dt>
                        <dd class="col-7 mb-0">
                            @forelse ($user->getRoleNames() as $role)
                                <span class="badge rounded-pill role-badge">{{ ucfirst($role) }}</span>
                            @empty
                                <span class="text-muted">Belum memiliki role</span>
                            @endforelse
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="surface-card mt-4 border-danger-subtle">
                <div class="surface-card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
