@extends('layouts.admin', ['activePage' => 'blog-posts', 'titlePage' => 'All Posts'])
@section('content')
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-post mr-2"></i> Published Posts</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Posts</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <!-- Stats Row -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-center py-3" style="border-left: 4px solid #28bf96;">
                    <div class="card-body p-2">
                        <h5 class="mb-0" style="color:#28bf96;">{{ \App\Models\Post::where('status','published')->count() }}</h5>
                        <small class="text-muted">Published</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center py-3" style="border-left: 4px solid #ef7f1a;">
                    <div class="card-body p-2">
                        <h5 class="mb-0" style="color:#ef7f1a;">{{ \App\Models\Post::where('status','draft')->count() }}</h5>
                        <small class="text-muted">Drafts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center py-3" style="border-left: 4px solid #fc5a5a;">
                    <div class="card-body p-2">
                        <h5 class="mb-0" style="color:#fc5a5a;">{{ \App\Models\Post::onlyTrashed()->count() }}</h5>
                        <small class="text-muted">Deleted</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center py-3" style="border-left: 4px solid #02408c;">
                    <div class="card-body p-2">
                        <h5 class="mb-0" style="color:#02408c;">{{ \App\Models\Post::sum('view_count') }}</h5>
                        <small class="text-muted">Total Views</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">All Published Posts</h4>
                    <a href="{{ route('blog-post.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Add New Post
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th style="width:40%">Post</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Views</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($post->thumbnail)
                                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt=""
                                                style="width:55px;height:45px;object-fit:cover;border-radius:6px;margin-right:12px;flex-shrink:0;">
                                        @else
                                            <div style="width:55px;height:45px;background:#e9ecef;border-radius:6px;margin-right:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="mdi mdi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold" style="font-size:14px;color:#253237;">{{ Str::limit($post->title, 55) }}</div>
                                            <small class="text-muted">/{{ $post->slug }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($post->category)
                                        <span class="badge" style="background:#e8f8f3;color:#28bf96;padding:5px 10px;border-radius:20px;font-size:12px;">
                                            {{ $post->category->title }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size:12px;">—</span>
                                    @endif
                                </td>
                                <td><small>{{ $post->author->name ?? '—' }}</small></td>
                                <td><span class="badge badge-light"><i class="fa fa-eye mr-1"></i>{{ $post->view_count }}</span></td>
                                <td><small>{{ $post->published_at ? $post->published_at->format('d M Y') : '—' }}</small></td>
                                <td>
                                    <a href="{{ route('blog-post.show', $post->id) }}" class="btn btn-sm btn-success mr-1" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('blog-post.edit', $post->id) }}" class="btn btn-sm btn-info mr-1" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('blog-post.destroys', $post->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="mdi mdi-post-outline" style="font-size:40px;color:#ccc;"></i>
                                    <p class="text-muted mt-2">No published posts yet. <a href="{{ route('blog-post.create') }}">Create one</a></p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $posts->links() }}</div>
            </div>
        </div>

    </div>
</div>
<script>
$(document).ready(function() {
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Move to Recycle Bin?',
            text: "You can restore it later.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fc5a5a',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endsection
