<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->whereIn('status', ['pending', 'awaiting_manager'])->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        $byDepartment = (clone $query)
            ->join('employees', 'employees.id', '=', 'permissions.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->selectRaw("coalesce(departments.name, employees.legacy_department, 'Bo\\'lim ko\\'rsatilmagan') as department_name, count(*) as total")
            ->groupBy('department_name')
            ->orderByDesc('total')
            ->get();

        $permissions = (clone $query)->with(['employee.department', 'category', 'approver'])
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();
        $employees = Employee::orderBy('full_name')->get();

        return view('reports.index', compact('stats', 'byDepartment', 'permissions', 'departments', 'employees'));
    }

    public function export(Request $request)
    {
        $rows = $this->filteredQuery($request)->with(['employee.department', 'category'])->latest('id')->get();

        $filename = 'hisobot_'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'Xodim', "Bo'lim", 'Kategoriya', 'Sabab', 'Boshlanish', 'Tugash', 'Holat', 'Kod']);

            foreach ($rows as $permission) {
                fputcsv($out, [
                    $permission->id,
                    $permission->employee->full_name ?? $permission->employee_name ?? '—',
                    $permission->employee?->department?->name ?? $permission->employee?->legacy_department ?? '—',
                    $permission->category->name ?? '—',
                    $permission->reason ?? $permission->destination ?? '—',
                    optional($permission->from_time)->format('d.m.Y H:i'),
                    optional($permission->to_time)->format('d.m.Y H:i'),
                    $permission->status,
                    $permission->code ?? '—',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function filteredQuery(Request $request)
    {
        $user = $request->user();
        $query = Permission::query();

        if ($user->isManager()) {
            $query->where('approver_id', $user->id);
        }

        if ($request->filled('from')) {
            $query->whereDate('from_time', '>=', Carbon::parse($request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('to_time', '<=', Carbon::parse($request->to));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $request->department_id));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        return $query;
    }
}
