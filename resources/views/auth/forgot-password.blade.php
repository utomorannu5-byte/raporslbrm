<x-guest-layout>
    <h1 class="auth-title">Lupa password</h1>
    <p class="auth-subtitle">
        Masukkan email akun Anda. Kami akan mengirimkan tautan untuk membuat password baru.
    </p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-4">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                required
                autofocus
                autocomplete="username"
                placeholder="nama@sekolah.test"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Kirim tautan reset</button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="small text-brand">Kembali ke halaman masuk</a>
    </div>
</x-guest-layout>
