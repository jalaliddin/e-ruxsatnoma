<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Services\TurnstileService;
use Illuminate\Http\Request;

class DoorAccessController extends Controller
{
    public function show(string $code)
    {
        return view('door.show', compact('code'));
    }

    public function open(Request $request, string $code, TurnstileService $turnstile)
    {
        $data = $request->validate([
            'direction' => 'required|in:kirish,chiqish',
        ]);

        $permission = Permission::where('code', $code)->where('status', 'approved')->first();

        if (! $permission || ! now()->between($permission->from_time, $permission->to_time)) {
            return back()->with('result', "❌ Kod yaroqsiz yoki muddati o'tgan.");
        }

        $device = $data['direction'] === 'kirish'
            ? config('services.turnstile.entry_device')
            : config('services.turnstile.exit_device');

        try {
            $turnstile->open($device);
        } catch (\Throwable) {
            return back()->with('result', "❌ Turniket bilan bog'lanib bo'lmadi. Qayta urinib ko'ring.");
        }

        return back()->with(
            'result',
            $data['direction'] === 'kirish' ? '✅ Kirish uchun eshik ochildi.' : '✅ Chiqish uchun eshik ochildi.'
        );
    }
}
