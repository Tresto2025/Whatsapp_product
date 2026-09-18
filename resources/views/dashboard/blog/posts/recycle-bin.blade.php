@extends('layouts.admin', ['activePage' => 'blog-posts', 'titlePage' => 'Recycle Bin'])
@section('content')
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-delete-restore mr-2"></i> Recycle Bin</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Recycle Bin</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="mdi mdi-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Deleted Posts</h4>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th style="width:50%">Post</th>
                                <th>Author</th>
                                <th>Deleted At</th>
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
                                                style="width:55px;height:45px;object-fit:cover;border-radius:6px;margin-right:12px;opacity:0.6;flex-shrink:0;">
                                        @else
                                            <div style="width:55px;height:45px;background:#e9ecef;border-radius:6px;margin-right:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="mdi mdi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold" style="font-size:14px;color:#999;text-decoration:line-through;">{{ Str::limit($post->title, 55) }}</div>
                                            <span class="badge" style="background:#ffeaea;color:#fc5a5a;font-size:11px;border-radius:20px;padding:2px 8px;">Deleted</span>
                                        </div>
                                    </div>
                                </td>
                                <td><small>{{ $post->author->name ?? '—' }}</small></td>
                                <td><small>{{ $post->deleted_at->format('d M Y, H:i') }}</small></td>
                                <td>
                                    <form action="{{ route('blog-post.restore', $post->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mr-1" title="Restore">
                                            <i class="mdi mdi-restore"></i> Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('blog-post.force-delete', $post->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn" title="Delete Permanently">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="mdi mdi-delete-empty" style="font-size:40px;color:#ccc;"></i>
                                    <p class="text-muted mt-2">Recycle bin is empty.</p>
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
            title: 'Permanently Delete?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fc5a5a',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete forever!'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endsection
