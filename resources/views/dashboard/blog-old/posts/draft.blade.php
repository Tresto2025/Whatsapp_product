@extends('layouts.admin')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Draft Posts</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Draft Posts</li>
                </ol>
            </nav>
        </div>
        
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">All Draft Posts</h4>
                            <a href="{{ route('blog-post.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus"></i> Add New Post
                            </a>
                        </div>
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Categories</th>
                                        <th>Last Modified</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($posts as $post)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($post->thumbnail)
                                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="" class="mr-2" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                @endif
                                                <div>
                                                    <strong>{{ Str::limit($post->title, 50) }}</strong>
                                                    <br><small class="text-muted">{{ $post->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $post->author->name ?? 'N/A' }}</td>
                                        <td>{{ $post->categories ?? 'Uncategorized' }}</td>
                                        <td>{{ $post->updated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('blog-post.edit', $post->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('blog-RecycleBinPost') }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $post->id }}">
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No draft posts found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This post will be moved to recycle bin!",
            showCancelButton: true,
            confirmButtonColor: '#fb6421',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
