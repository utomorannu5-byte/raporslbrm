<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display the user management index.
     */
    public function index(): View
    {
        $users = User::with(['roles' => function ($query) {
            $query->where('guard_name', 'web');
        }])->get();

        $roles = Role::where('guard_name', 'web')->get();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a new user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil ditambahkan.');
    }

    /**
     * Display the edit form for a user.
     */
    public function edit(User $user): View
    {
        $roles = Role::where('guard_name', 'web')->get();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update a user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        // Only update password if provided
        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil diperbarui.');
    }

    /**
     * Remove a user (soft delete not allowed for own account).
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent users from deleting themselves
        if ($user->id === auth()->id()) {
            return back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil dihapus.');
    }
}