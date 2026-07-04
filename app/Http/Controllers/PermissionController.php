<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\User;
use App\Services\TelegramService;
use App\Services\TurnstileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Permission::with(['user', 'employee', 'category', 'approver', 'hr']);

        if ($user->isManager()) {
            $query->where('approver_id', $user->id);
        } elseif ($user->isHr()) {
            $query->whereIn('status', ['pending', 'awaiting_manager'])->orWhere('hr_id', $user->id);
        } elseif (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $permissions = $query->latest('id')->paginate(25);

        $managers = $user->isHr() || $user->isAdmin()
            ? User::where('role', 'manager')->get()
            : collect();

        return view('index', compact('permissions', 'managers'));
    }

    public function show(Permission $permission)
    {
        $permission->load(['user', 'employee', 'category', 'approver', 'hr']);

        return view('permissions.show', compact('permission'));
    }

    public function assignManager(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'approver_id' => 'required|exists:users,id',
        ]);

        $permission->update([
            'approver_id' => $data['approver_id'],
            'hr_id' => $request->user()->id,
            'status' => 'awaiting_manager',
        ]);

        $manager = User::find($data['approver_id']);
        if ($manager?->telegram_chat_id) {
            app(TelegramService::class)->sendManagerDecisionRequest($permission, $manager);
        }

        return back()->with('success', 'So\'rov rahbarga yuborildi.');
    }

    public function decide(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'decision' => 'required|in:approve,reject',
        ]);

        if ($data['decision'] === 'approve') {
            $permission->update([
                'code' => $this->generateCode(),
                'status' => 'approved',
                'approver_id' => $permission->approver_id ?: $request->user()->id,
                'decided_at' => now(),
            ]);

            if ($permission->employee?->telegram_chat_id) {
                app(TelegramService::class)->sendApprovalToEmployee($permission);
            }
        } else {
            $permission->update([
                'status' => 'rejected',
                'approver_id' => $permission->approver_id ?: $request->user()->id,
                'decided_at' => now(),
            ]);

            if ($permission->employee?->telegram_chat_id) {
                app(TelegramService::class)->sendRejectionToEmployee($permission);
            }
        }

        return back()->with('success', 'Qaror qabul qilindi.');
    }

    public function edit(Permission $permission)
    {
        $categories = PermissionCategory::where('is_active', true)->get();
        $employees = Employee::orderBy('full_name')->get();

        return view('permissions.edit', compact('permission', 'categories', 'employees'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'category_id' => 'nullable|exists:permission_categories,id',
            'reason' => 'nullable|string',
            'from_time' => 'required|date',
            'to_time' => 'required|date|after:from_time',
        ]);

        $permission->update($data);

        return redirect()->route('permission.show', $permission)->with('success', 'So\'rov yangilandi.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permission.index')->with('success', 'So\'rov o\'chirildi.');
    }

    public function create()
    {
        $categories = PermissionCategory::where('is_active', true)->get();
        $managers = User::where('role', 'manager')->get();

        return view('create', compact('categories', 'managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string',
            'destination' => 'required|string',
            'from_time' => 'required|date',
            'to_time' => 'required|date|after:from_time',
        ]);

        $permission = Permission::create([
            'user_id' => Auth::id(),
            'employee_name' => $request->employee_name,
            'destination' => $request->destination,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
            'code' => $this->generateCode(),
            'status' => 'approved',
        ]);

        return view('create', ['code' => $permission->code]);
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

        $permission = Permission::where('code', $request->code)->where('status', 'approved')->first();

        if (! $permission) {
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
        $body = app(TurnstileService::class)->open($device);

        return response()->json([
            'response' => $body,
        ]);
    }

    public function webhook(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $permission = Permission::where('code', $request->code)->where('status', 'approved')->first();

        if (! $permission) {
            return response()->json('not found');
        }

        $now = now();
        if ($now->between($permission->from_time, $permission->to_time)) {
            $this->openDoor($request->device);
            return response()->json('success');
        } else {
            return response()->json('expired');
        }
    }

    private function generateCode(): string
    {
        do {
            $code = (string) random_int(1000, 9999);
        } while (Permission::where('code', $code)->exists());

        return $code;
    }
}
