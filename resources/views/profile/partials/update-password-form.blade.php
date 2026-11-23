<section>
    <header>
        <h2 class="section-title">Ubah Password</h2>
        <p class="section-description">Gunakan password yang panjang dan unik agar akun tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Password saat ini</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="form-control @if ($errors->updatePassword->get('current_password')) is-invalid @endif"
                autocomplete="current-password"
            >
            @if ($errors->updatePassword->get('current_password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Password baru</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                class="form-control @if ($errors->updatePassword->get('password')) is-invalid @endif"
                autocomplete="new-password"
            >
            @if ($errors->updatePassword->get('password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Konfirmasi password baru</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control @if ($errors->updatePassword->get('password_confirmation')) is-invalid @endif"
                autocomplete="new-password"
            >
            @if ($errors->updatePassword->get('password_confirmation'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Simpan password</button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="small text-success"
                >Tersimpan.</span>
            @endif
        </div>
    </form>
</section>
