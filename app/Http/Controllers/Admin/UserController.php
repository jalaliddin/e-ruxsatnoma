<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'hr', 'manager'])->latest()->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:32',
            'role' => 'required|in:admin,hr,manager',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi yaratildi.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:32',
            'role' => 'required|in:admin,hr,manager',
            'password' => 'nullable|string|min:6',
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi yangilandi.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Foydalanuvchi o\'chirildi.');
    }

    public function generateTelegramLink(User $user)
    {
        $token = Str::random(24);
        $user->update(['telegram_link_token' => $token]);

        $botUsername = config('services.telegram.bot_username');
        $link = $botUsername ? "https://t.me/{$botUsername}?start=link_{$token}" : null;

        return redirect()->route('admin.users.index')->with(
            'success',
            $link
                ? "Telegram bog'lash havolasi: {$link}"
                : "Bog'lash tokeni yaratildi ({$token}), lekin TELEGRAM_BOT_USERNAME sozlanmagan."
        );
    }
}
