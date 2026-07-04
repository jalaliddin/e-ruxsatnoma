<?php

namespace App\Http\Controllers;

use App\Models\PermissionCategory;
use Illuminate\Http\Request;

class PermissionCategoryController extends Controller
{
    public function index()
    {
        $categories = PermissionCategory::latest()->paginate(25);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:permission_categories,name',
            'is_active' => 'nullable|boolean',
        ]);

        PermissionCategory::create([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategoriya qo\'shildi.');
    }

    public function edit(PermissionCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, PermissionCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:permission_categories,name,'.$category->id,
        ]);

        $category->update([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategoriya yangilandi.');
    }

    public function destroy(PermissionCategory $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategoriya o\'chirildi.');
    }
}
