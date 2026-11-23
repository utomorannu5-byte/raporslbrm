<x-guest-layout>
    <h1 class="auth-title">Konfirmasi password</h1>
    <p class="auth-subtitle">
        Area ini memerlukan konfirmasi. Masukkan password Anda sebelum melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-4">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                required
                autocomplete="current-password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Konfirmasi</button>
    </form>
</x-guest-layout>
