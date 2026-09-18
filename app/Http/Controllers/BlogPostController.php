<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    /**
     * Display a listing of published posts.
     */
    public function index()
    {
        $posts = Post::with('author')
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(10);
        
        return view('dashboard.blog.posts.index', compact('posts'));
    }

    /**
     * Display draft posts.
     */
    public function draft()
    {
        $posts = Post::with('author')
            ->where('status', 'draft')
            ->latest('updated_at')
            ->paginate(10);
        
        return view('dashboard.blog.posts.draft', compact('posts'));
    }

    /**
     * Display soft deleted posts (recycle bin).
     */
    public function recycleBin()
    {
        $posts = Post::onlyTrashed()
            ->with('author')
            ->latest('deleted_at')
            ->paginate(10);
        
        return view('dashboard.blog.posts.recycle-bin', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::all();
        return view('dashboard.blog.posts.create', compact('categories'));
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'keyword' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'category_id' => 'nullable|string',
        ]);
        // dd($validated);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']);
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog/thumbnails', 'public');
        }

        // Set published_at if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        Post::create($validated);

        return redirect()->route('blog-post.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::all();
        
        return view('dashboard.blog.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'keyword' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'categories' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog/thumbnails', 'public');
        }

        // Set published_at if status changed to published
        if ($validated['status'] === 'published' && $post->status !== 'published') {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect()->route('blog-post.index')
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Soft delete the specified post.
     */
    public function destroy(Request $request)
    {
        $post = Post::findOrFail($request->id);
        $post->delete();

        return redirect()->back()
            ->with('success', 'Post moved to recycle bin!');
    }

    /**
     * Restore a soft deleted post.
     */
    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect()->route('blog-post.recycle-bin')
            ->with('success', 'Post restored successfully!');
    }

    /**
     * Permanently delete the specified post.
     */
    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        
        // Delete thumbnail if exists
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        
        $post->forceDelete();

        return redirect()->route('blog-post.recycle-bin')
            ->with('success', 'Post permanently deleted!');
    }
}
