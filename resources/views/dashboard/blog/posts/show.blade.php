@extends('layouts.admin', ['activePage' => 'blog-posts', 'titlePage' => 'View Post'])
@section('content')
<style>
    .post-content img { max-width: 100%; height: auto; border-radius: 6px; margin: 10px 0; }
    .post-content p { margin-bottom: 1rem; line-height: 1.8; }
    .post-content h1, .post-content h2, .post-content h3, .post-content h4 { margin: 1.5rem 0 0.75rem; font-weight: 600; }
    .post-content ul, .post-content ol { padding-left: 1.5rem; margin-bottom: 1rem; }
    .post-content blockquote { border-left: 4px solid #28bf96; padding: 10px 16px; background: #f0faf7; margin: 1rem 0; border-radius: 0 6px 6px 0; }
    .post-content pre { background: #f4f4f4; padding: 14px; border-radius: 6px; overflow-x: auto; }
    .post-content a { color: #28bf96; }
</style>
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-eye mr-2" style="color:#28bf96;"></i>View Post</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog-post.index') }}">Posts</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-8">

                {{-- Post Content --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 style="font-size:24px;font-weight:700;color:#253237;margin-bottom:16px;">{{ $post->title }}</h2>

                        <div class="d-flex align-items-center mb-4" style="gap:16px;flex-wrap:wrap;">
                            @if($post->category)
                            <span style="background:#e8f8f3;color:#28bf96;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                                <i class="mdi mdi-tag mr-1"></i>{{ $post->category->title }}
                            </span>
                            @endif
                            <span style="font-size:12px;color:#999;">
                                <i class="mdi mdi-account mr-1"></i>{{ $post->author->name ?? '—' }}
                            </span>
                            <span style="font-size:12px;color:#999;">
                                <i class="mdi mdi-calendar mr-1"></i>
                                {{ $post->published_at ? $post->published_at->format('d M Y') : 'Not published' }}
                            </span>
                            <span style="font-size:12px;color:#999;">
                                <i class="fa fa-eye mr-1"></i>{{ $post->view_count }} views
                            </span>
                            <span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;
                                background:{{ $post->status === 'published' ? '#e8f8f3' : '#fff3e0' }};
                                color:{{ $post->status === 'published' ? '#28bf96' : '#ef7f1a' }};">
                                {{ ucfirst($post->status) }}
                            </span>
                        </div>

                        @if($post->thumbnail)
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                            style="width:100%;max-height:400px;object-fit:cover;border-radius:8px;margin-bottom:24px;">
                        @endif

                        @if($post->excerpt)
                        <div style="background:#f8fffe;border-left:4px solid #28bf96;padding:12px 16px;border-radius:0 6px 6px 0;margin-bottom:24px;font-style:italic;color:#555;">
                            {{ $post->excerpt }}
                        </div>
                        @endif

                        <div class="post-content">
                            {!! $post->content !!}
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                {{-- Actions --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#28bf96;padding-bottom:8px;border-bottom:2px solid #e8f8f3;margin-bottom:16px;">Actions</p>
                        <a href="{{ route('blog-post.edit', $post->id) }}" class="btn btn-primary btn-block mb-2">
                            <i class="fa fa-edit mr-1"></i> Edit Post
                        </a>
                        <a href="{{ route('blog-post.index') }}" class="btn btn-light btn-block">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Posts
                        </a>
                    </div>
                </div>

                {{-- SEO --}}
                @if($post->seo_title || $post->keyword || $post->description)
                <div class="card mb-3">
                    <div class="card-body">
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#28bf96;padding-bottom:8px;border-bottom:2px solid #e8f8f3;margin-bottom:16px;">SEO Info</p>
                        @if($post->seo_title)
                        <div class="mb-2">
                            <small class="text-muted d-block">SEO Title</small>
                            <span style="font-size:13px;">{{ $post->seo_title }}</span>
                        </div>
                        @endif
                        @if($post->keyword)
                        <div class="mb-2">
                            <small class="text-muted d-block">Keywords</small>
                            <span style="font-size:13px;">{{ $post->keyword }}</span>
                        </div>
                        @endif
                        @if($post->description)
                        <div>
                            <small class="text-muted d-block">Meta Description</small>
                            <span style="font-size:13px;">{{ $post->description }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
