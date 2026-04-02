<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->where(fn ($query) => $query->where('is_admin', true)->orWhere('is_writer', true))
            ->withCount('posts')
            ->latest()
            ->get(['id', 'name', 'email', 'is_admin', 'is_writer', 'created_at'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->is_admin ? 'admin' : 'writer',
                'posts_count' => $user->posts_count,
                'created_at' => $user->created_at?->toDateTimeString(),
                'is_current_user' => (int) $user->id === (int) request()->user()->id,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'userMetrics' => [
                'total' => $users->count(),
                'admins' => $users->where('role', 'admin')->count(),
                'writers' => $users->where('role', 'writer')->count(),
                'posts_total' => $users->sum('posts_count'),
            ],
        ]);
    }

    public function store(StoreAdminUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['role'] === 'admin',
            'is_writer' => $validated['role'] === 'writer',
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario criado com sucesso.');
    }

    public function update(UpdateAdminUserRequest $request, User $user)
    {
        abort_unless($user->is_admin || $user->is_writer, 404);

        $validated = $request->validated();

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $validated['role'] === 'admin',
            'is_writer' => $validated['role'] === 'writer',
        ];

        if ($user->isOnlyRemainingAdmin() && $validated['role'] !== 'admin') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Nao e possivel remover o papel do unico administrador.');
        }

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($user->is_admin || $user->is_writer, 404);

        $adminsCount = User::query()->where('is_admin', true)->count();
        $isSelfDeletion = (int) $request->user()->id === (int) $user->id;

        if ($isSelfDeletion && $adminsCount <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Nao e possivel excluir sua propria conta sendo o unico administrador.');
        }

        $user->delete();

        if ($isSelfDeletion) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Sua conta foi removida.');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario removido com sucesso.');
    }
}
