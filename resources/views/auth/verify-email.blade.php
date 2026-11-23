<x-guest-layout>
    <h1 class="auth-title">Verifikasi email</h1>
    <p class="auth-subtitle">
        Sebelum melanjutkan, verifikasi alamat email Anda melalui tautan yang baru kami kirimkan.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small py-2 px-3 mt-4 mb-0">
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-primary w-100">Kirim ulang email verifikasi</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-link btn-sm w-100 text-muted">Keluar</button>
    </form>
</x-guest-layout>
