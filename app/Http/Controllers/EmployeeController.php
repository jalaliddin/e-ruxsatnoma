<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(25);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:32|unique:employees,phone',
            'department' => 'nullable|string|max:255',
        ]);

        Employee::create($data + ['created_by' => $request->user()->id]);

        return redirect()->route('employees.index')->with('success', 'Xodim ro\'yxatga olindi.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:32|unique:employees,phone,'.$employee->id,
            'department' => 'nullable|string|max:255',
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
