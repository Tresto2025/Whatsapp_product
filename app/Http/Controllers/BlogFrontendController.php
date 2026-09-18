<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogFrontendController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category', 'author'])
                    ->where('status', 'published')
                    ->orderBy('published_at', 'desc')
                    ->paginate(12);

        return view('frontend.blog', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::with(['category', 'author'])
                   ->where('status', 'published')
                   ->where('slug', $slug)
                   ->firstOrFail();

        // Increment view count
        $post->increment('view_count');

        // Get categories with post count
        $categories = Category::withCount('posts')->get();
        
        // Get top 5 most viewed posts
        $topPosts = Post::where('status', 'published')
                       ->where('id', '!=', $post->id)
                       ->orderBy('view_count', 'desc')
                       ->limit(5)
                       ->get();
        
        // Get recent posts
        $recentPosts = Post::where('status', 'published')
                          ->where('id', '!=', $post->id)
                          ->orderBy('published_at', 'desc')
                          ->limit(5)
                          ->get();

        return view('frontend.blog-detail', compact('post', 'categories', 'topPosts', 'recentPosts'));
    }
}