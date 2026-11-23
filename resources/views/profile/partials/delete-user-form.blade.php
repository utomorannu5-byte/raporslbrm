<section>
    <header>
        <h2 class="section-title text-danger">Hapus Akun</h2>
        <p class="section-description">
            Setelah dihapus, seluruh data akun ini tidak dapat dipulihkan. Masukkan password untuk mengonfirmasi.
        </p>
    </header>

    <form
        method="post"
        action="{{ route('profile.destroy') }}"
        class="mt-4"
        onsubmit="return confirm('Hapus akun ini secara permanen?');"
    >
        @csrf
        @method('delete')

        <div class="mb-3">
            <label for="delete_account_password" class="form-label">Password</label>
            <input
                id="delete_account_password"
                name="password"
                type="password"
                class="form-control @if ($errors->userDeletion->get('password')) is-invalid @endif"
                placeholder="Masukkan password"
            >
            @if ($errors->userDeletion->get('password'))
                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
            @endif
        </div>

        <button type="submit" class="btn btn-outline-danger">Hapus akun</button>
    </form>
</section>
