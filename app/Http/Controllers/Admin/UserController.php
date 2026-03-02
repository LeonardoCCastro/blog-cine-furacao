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
        return Inertia::render('Admin/Users/Index', [
            'admins' => User::query()
                ->where('is_admin', true)
                ->latest()
                ->get(['id', 'name', 'email', 'created_at'])
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at?->toDateTimeString(),
                ]),
        ]);
    }

    public function store(StoreAdminUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Administrador criado com sucesso.');
    }

    public function update(UpdateAdminUserRequest $request, User $user)
    {
        abort_unless($user->is_admin, 404);

        $validated = $request->validated();

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Administrador atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($user->is_admin, 404);

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

            return redirect('/')->with('success', 'Sua conta de administrador foi removida.');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Administrador removido com sucesso.');
    }
}
