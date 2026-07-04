<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')->latest()->paginate(25);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:32|unique:employees,phone',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        Employee::create($data + ['created_by' => $request->user()->id]);

        return redirect()->route('employees.index')->with('success', 'Xodim ro\'yxatga olindi.');
    }

    public function edit(Employee $employee)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:32|unique:employees,phone,'.$employee->id,
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Xodim ma\'lumotlari yangilandi.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Xodim o\'chirildi.');
    }
}
