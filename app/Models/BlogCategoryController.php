<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('dashboard.blog.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:categories,title',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        Category::create($validated);

        return redirect()->route('blog-category.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::latest()->paginate(10);
        
        return view('dashboard.blog.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:categories,title,' . $id,
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $category->update($validated);

        return redirect()->route('blog-category.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->delete();

        return redirect()->route('blog-category.index')
            ->with('success', 'Category deleted successfully!');
    }
}
