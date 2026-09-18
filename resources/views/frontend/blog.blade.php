@extends('frontend.layout.app')

@section('content')
<style>
    .blog-hero {
        background: linear-gradient(135deg, #28bf96 0%, #1a9d7a 100%);
        padding: 80px 0 60px;
        color: white;
        margin-bottom: 50px;
    }
    .blog-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .blog-hero p {
        font-size: 1.2rem;
        opacity: 0.95;
    }
    .blog-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        height: 100%;
    }
    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    .blog-card-img {
        height: 250px;
        object-fit: cover;
        width: 100%;
    }
    .blog-card-img-placeholder {
        height: 250px;
        background: linear-gradient(135deg, #e8f8f3 0%, #d4f1e8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: #28bf96;
    }
    .blog-card-body {
        padding: 25px;
    }
    .blog-card-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #253237;
        margin-bottom: 15px;
        line-height: 1.4;
    }
    .blog-card-title:hover {
        color: #28bf96;
    }
    .blog-card-excerpt {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 20px;
    }
    .blog-card-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.85rem;
        color: #999;
        margin-bottom: 20px;
    }
    .blog-card-meta i {
        color: #28bf96;
    }
    .blog-category-badge {
        display: inline-block;
        background: #e8f8f3;
        color: #28bf96;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .btn-read-more {
        background: #28bf96;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-read-more:hover {
        background: #1a9d7a;
        color: white;
        transform: translateX(5px);
    }
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }
    .empty-state i {
        font-size: 5rem;
        color: #e0e0e0;
        margin-bottom: 20px;
    }
    .empty-state h3 {
        color: #666;
        margin-bottom: 10px;
    }
    .empty-state p {
        color: #999;
    }
</style>

<div class="blog-hero">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1>Our Blog</h1>
                <p>Insights, stories, and updates from our team</p>
            </div>
        </div>
    </div>
</div>

<section class="blog-section pb-5">
    <div class="container">
        <div class="row">
            @forelse($posts ?? [] as $post)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="blog-card">
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="blog-card-img" alt="{{ $post->title }}">
                        @else
                            <div class="blog-card-img-placeholder">
                                <i class="mdi mdi-post"></i>
                            </div>
                        @endif
                        <div class="blog-card-body">
                            @if($post->category)
                                <span class="blog-category-badge">{{ $post->category->title }}</span>
                            @endif
                            <h5 class="blog-card-title">
                                <a href="{{ route('blog.detail', $post->slug) }}" style="text-decoration: none; color: inherit;">
                                    {{ $post->title }}
                                </a>
                            </h5>
                            <div class="blog-card-meta">
                                <span><i class="mdi mdi-calendar"></i> {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                                @if($post->view_count > 0)
                                    <span><i class="mdi mdi-eye"></i> {{ $post->view_count }} views</span>
                                @endif
                            </div>
                            <p class="blog-card-excerpt">
                                {{ Str::limit(strip_tags($post->excerpt ?? $post->content), 120) }}
                            </p>
                            <a href="{{ route('blog.detail', $post->slug) }}" class="btn-read-more">
                                Read More <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="mdi mdi-post-outline"></i>
                        <h3>No Posts Yet</h3>
                        <p>Check back soon for new content!</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        @if(isset($posts) && $posts->hasPages())
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection