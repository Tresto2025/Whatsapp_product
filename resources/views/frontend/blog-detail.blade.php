@extends('frontend.layout.app')

@section('content')
<style>
    /* Clean WordPress-style blog design with sidebar */
    .blog-wrapper {
        background: #f5f5f5;
        padding: 40px 0 60px;
    }
    
    /* Simple breadcrumb */
    .simple-breadcrumb {
        background: white;
        padding: 15px 0;
        margin-bottom: 30px;
        border-bottom: 1px solid #e5e5e5;
    }
    
    .simple-breadcrumb a {
        color: #28bf96;
        text-decoration: none;
        font-size: 14px;
    }
    
    .simple-breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .simple-breadcrumb span {
        color: #999;
        margin: 0 8px;
    }
    
    /* Main content area */
    .blog-main-content {
        background: white;
        padding: 40px;
        border: 1px solid #e5e5e5;
    }
    
    /* Article header */
    .article-header {
        margin-bottom: 30px;
    }
    
    .article-category {
        display: inline-block;
        background: #28bf96;
        color: white;
        padding: 5px 15px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 15px;
        letter-spacing: 0.5px;
    }
    
    .article-title {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        line-height: 1.3;
        margin-bottom: 15px;
    }
    
    .article-excerpt {
        font-size: 16px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    .article-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 0;
        border-top: 1px solid #e5e5e5;
        border-bottom: 1px solid #e5e5e5;
        margin-bottom: 30px;
        font-size: 13px;
        color: #666;
        flex-wrap: wrap;
    }
    
    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .article-meta-item i {
        color: #28bf96;
    }
    
    /* Featured image */
    .article-featured-image {
        width: 100%;
        height: auto;
        margin-bottom: 30px;
        border: 1px solid #e5e5e5;
    }
    
    /* Article content */
    .article-content {
        font-size: 16px;
        line-height: 1.8;
        color: #333;
    }
    
    .article-content p {
        margin-bottom: 20px;
    }
    
    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4 {
        font-weight: 700;
        color: #333;
        margin: 25px 0 15px;
        line-height: 1.3;
    }
    
    .article-content h1 { font-size: 28px; }
    .article-content h2 { font-size: 24px; }
    .article-content h3 { font-size: 20px; }
    .article-content h4 { font-size: 18px; }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        margin: 20px 0;
        border: 1px solid #e5e5e5;
    }
    
    .article-content ul,
    .article-content ol {
        margin: 20px 0;
        padding-left: 30px;
    }
    
    .article-content li {
        margin-bottom: 10px;
    }
    
    .article-content blockquote {
        border-left: 4px solid #28bf96;
        padding: 15px 20px;
        background: #f9f9f9;
        margin: 20px 0;
        font-style: italic;
        color: #555;
    }
    
    .article-content a {
        color: #28bf96;
        text-decoration: underline;
    }
    
    .article-content a:hover {
        color: #1a9d7a;
    }
    
    .article-content table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
        border: 1px solid #e5e5e5;
    }
    
    .article-content table th {
        background: #f9f9f9;
        padding: 12px;
        text-align: left;
        font-weight: 700;
        border: 1px solid #e5e5e5;
    }
    
    .article-content table td {
        padding: 12px;
        border: 1px solid #e5e5e5;
    }
    
    .article-content code {
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: #c7254e;
    }
    
    .article-content pre {
        background: #f4f4f4;
        padding: 15px;
        border: 1px solid #e5e5e5;
        overflow-x: auto;
        margin: 20px 0;
    }
    
    .article-content pre code {
        background: none;
        color: #333;
        padding: 0;
    }
    
    /* Tags section */
    .article-tags {
        padding: 20px 0;
        border-top: 1px solid #e5e5e5;
        margin-top: 30px;
    }
    
    .article-tags-title {
        font-size: 14px;
        font-weight: 700;
        color: #666;
        margin-bottom: 10px;
    }
    
    .article-tag {
        display: inline-block;
        background: #f4f4f4;
        color: #666;
        padding: 5px 12px;
        border-radius: 3px;
        margin: 5px 5px 5px 0;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid #e5e5e5;
    }
    
    .article-tag:hover {
        background: #28bf96;
        color: white;
        border-color: #28bf96;
    }
    
    /* Sidebar */
    .blog-sidebar {
        position: sticky;
        top: 20px;
    }
    
    .sidebar-widget {
        background: white;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e5e5e5;
    }
    
    .sidebar-widget-title {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #28bf96;
    }
    
    /* Categories widget */
    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .category-item {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .category-item:last-child {
        border-bottom: none;
    }
    
    .category-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #666;
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .category-link:hover {
        color: #28bf96;
    }
    
    .category-count {
        background: #f4f4f4;
        color: #666;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
    }
    
    /* Posts widget */
    .post-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .post-item {
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .post-item:last-child {
        border-bottom: none;
    }
    
    .post-link {
        color: #333;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        display: block;
        margin-bottom: 5px;
    }
    
    .post-link:hover {
        color: #28bf96;
    }
    
    .post-meta-small {
        font-size: 12px;
        color: #999;
    }
    
    .post-meta-small i {
        color: #28bf96;
        margin-right: 3px;
    }
    
    /* Back button */
    .back-to-blog {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e5e5;
    }
    
    .back-button {
        display: inline-block;
        background: #28bf96;
        color: white;
        padding: 10px 25px;
        border-radius: 3px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }
    
    .back-button:hover {
        background: #1a9d7a;
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 991px) {
        .blog-main-content {
            padding: 30px 20px;
            margin-bottom: 30px;
        }
        
        .sidebar-widget {
            padding: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .article-title {
            font-size: 24px;
        }
        
        .article-content {
            font-size: 15px;
        }
    }
</style>

<!-- Breadcrumb -->
<div class="simple-breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('blog.index') }}">Blog</a>
        <span>/</span>
        <span style="color: #333;">{{ Str::limit($post->title, 50) }}</span>
    </div>
</div>

<div class="blog-wrapper">
    <div class="container">
        <div class="row">
            <!-- Main Content (Left Side) -->
            <div class="col-lg-8">
                <div class="blog-main-content">
                    <article>
                        <!-- Header -->
                        <header class="article-header">
                            @if($post->category)
                                <span class="article-category">{{ $post->category->title }}</span>
                            @endif
                            
                            <h1 class="article-title">{{ $post->title }}</h1>
                            
                            @if($post->excerpt)
                                <p class="article-excerpt">{{ $post->excerpt }}</p>
                            @endif
                            
                            <div class="article-meta">
                                @if($post->author)
                                    <div class="article-meta-item">
                                        <i class="mdi mdi-account"></i>
                                        <span>{{ $post->author->name }}</span>
                                    </div>
                                @endif
                                <div class="article-meta-item">
                                    <i class="mdi mdi-calendar"></i>
                                    <span>{{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}</span>
                                </div>
                                <div class="article-meta-item">
                                    <i class="mdi mdi-eye"></i>
                                    <span>{{ number_format($post->view_count) }} views</span>
                                </div>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="article-featured-image" alt="{{ $post->title }}">
                        @endif

                        <!-- Content -->
                        <div class="article-content">
                            {!! $post->content !!}
                        </div>

                        <!-- Tags -->
                        @if($post->keyword)
                            <div class="article-tags">
                                <div class="article-tags-title">Tags:</div>
                                <div>
                                    @foreach(explode(',', $post->keyword) as $tag)
                                        <span class="article-tag">{{ trim($tag) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </article>

                    <!-- Back Button -->
                    <div class="back-to-blog">
                        <a href="{{ route('blog.index') }}" class="back-button">
                            ← Back to Blog
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right Side) -->
            <div class="col-lg-4">
                <div class="blog-sidebar">
                    <!-- Categories Widget -->
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget-title">Categories</h3>
                        <ul class="category-list">
                            @forelse($categories as $category)
                                <li class="category-item">
                                    <a href="{{ route('blog.index') }}?category={{ $category->slug }}" class="category-link">
                                        <span>{{ $category->title }}</span>
                                        <span class="category-count">{{ $category->posts_count }}</span>
                                    </a>
                                </li>
                            @empty
                                <li class="category-item">
                                    <span style="color: #999;">No categories yet</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Top Posts Widget -->
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget-title">Popular Posts</h3>
                        <ul class="post-list">
                            @forelse($topPosts as $topPost)
                                <li class="post-item">
                                    <a href="{{ route('blog.detail', $topPost->slug) }}" class="post-link">
                                        {{ Str::limit($topPost->title, 60) }}
                                    </a>
                                    <div class="post-meta-small">
                                        <i class="mdi mdi-eye"></i> {{ number_format($topPost->view_count) }} views
                                    </div>
                                </li>
                            @empty
                                <li class="post-item">
                                    <span style="color: #999;">No posts yet</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Recent Posts Widget -->
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget-title">Recent Posts</h3>
                        <ul class="post-list">
                            @forelse($recentPosts as $recentPost)
                                <li class="post-item">
                                    <a href="{{ route('blog.detail', $recentPost->slug) }}" class="post-link">
                                        {{ Str::limit($recentPost->title, 60) }}
                                    </a>
                                    <div class="post-meta-small">
                                        <i class="mdi mdi-calendar"></i> {{ $recentPost->published_at ? $recentPost->published_at->format('M d, Y') : $recentPost->created_at->format('M d, Y') }}
                                    </div>
                                </li>
                            @empty
                                <li class="post-item">
                                    <span style="color: #999;">No posts yet</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection