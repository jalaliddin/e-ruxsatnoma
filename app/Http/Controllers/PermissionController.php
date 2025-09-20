<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class PermissionController extends Controller
{
    public function index()
    {
    $permissions = Permission::with('user')
        ->latest('id')
        ->paginate(25); // sahifalash 25 ta yozuv bilan
        return view('index', compact('permissions'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string',
            'destination' => 'required|string',
            'from_time' => 'required|date',
            'to_time' => 'required|date|after:from_time',
        ]);

        do {
            $code = rand(1000, 9999);
        } while (Permission::where('code', $code)->exists());

        $permission = Permission::create([
            'user_id' => Auth::id(),
            'employee_name' => $request->employee_name,
            'destination' => $request->destination,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
            'code' => $code,
        ]);

        return view('create', ['code' => $code]);
    }

    public function checkForm()
    {
        return view('check');
    }

    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $permission = Permission::where('code', $request->code)->first();

        if (!$permission) {
            return back()->with('result', '❌ Kod topilmadi.');
        }

        $now = now();
        if ($now->between($permission->from_time, $permission->to_time)) {
            return back()->with('result', '✅ Ruxsat bor. (muddat ichida)');
        } else {
            return back()->with('result', '❌ Ruxsat yo‘q yoki muddati o‘tgan.');
        }
    }

    public function openDoor($device)
    {
    $client = new Client([
        'base_uri' => 'http://10.100.90.5'.$device,
        'auth' => ['admin', '01x994ma', 'digest'],  // digest auth uchun
    ]);

    $response = $client->request('GET', '/cgi-bin/accessControl.cgi', [
        'query' => [
            'action' => 'openDoor',
            'channel' => 1,
        ],
    ]);

    $body = $response->getBody()->getContents();

    return response()->json([
        'response' => $body,
    ]);
    }

    public function webhook(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        // return response()->json($request->code);

        $permission = Permission::where('code', $request->code)->first();

        if (!$permission) {
            return response()->json('not found');
            //return back()->with('result', '❌ Kod topilmadi.');
        }

        $now = now();
        if ($now->between($permission->from_time, $permission->to_time)) {
            $this->openDoor($request->device);
            return response()->json('success');
            // return back()->with('result', '✅ Ruxsat bor. (muddat ichida)');
        } else {
            return response()->json('expired');
            // return back()->with('result', '❌ Ruxsat yo‘q yoki muddati o‘tgan.');
        }
    }
}
