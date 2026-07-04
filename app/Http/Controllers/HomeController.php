<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Permission;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = Permission::query();

        if ($user->isManager()) {
            $baseQuery->where('approver_id', $user->id);
        }

        $stats = [
            'pending' => (clone $baseQuery)->whereIn('status', ['pending', 'awaiting_manager'])->count(),
            'approvedToday' => (clone $baseQuery)->where('status', 'approved')->whereDate('decided_at', today())->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'employees' => Employee::count(),
        ];

        $recent = (clone $baseQuery)->with(['employee', 'category'])->latest('id')->take(8)->get();

        return view('home', compact('stats', 'recent'));
    }
}
