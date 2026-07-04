<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::latest()->paginate(25);

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'is_active' => 'nullable|boolean',
        ]);

        Department::create([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('departments.index')->with('success', "Bo'lim qo'shildi.");
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,'.$department->id,
        ]);

        $department->update([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('departments.index')->with('success', "Bo'lim yangilandi.");
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')->with('success', "Bo'lim o'chirildi.");
    }
}
