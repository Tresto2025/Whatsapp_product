@extends('layouts.admin', ['activePage' => 'blog-categories', 'titlePage' => 'Edit Category'])
@section('content')
<style>
    .blog-input {
        display: block !important; width: 100% !important; padding: 10px 14px !important;
        font-size: 14px !important; border: 1px solid #ced4da !important; border-radius: 6px !important;
        background: #fff !important; background-image: none !important; color: #253237 !important;
    }
    .blog-input:focus { border-color: #28bf96 !important; outline: 0 !important; box-shadow: 0 0 0 3px rgba(40,191,150,.12) !important; background-image: none !important; }
    .blog-label { font-size: 13px !important; font-weight: 600 !important; color: #253237 !important; margin-bottom: 8px !important; margin-top: 0 !important; display: block !important; line-height: 1.4 !important; }
    .form-group { margin-bottom: 0 !important; padding-top: 0 !important; }
    .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #28bf96; padding-bottom: 8px; border-bottom: 2px solid #e8f8f3; margin-bottom: 20px; }
</style>
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-tag-text-outline mr-2" style="color:#28bf96;"></i>Edit Category</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog-category.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="mdi mdi-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        @endif

        <div class="row">

            {{-- Edit Form --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <p class="section-title">Edit Category</p>

                        @if($errors->any())
                        <div class="alert alert-danger py-2 mb-3">
                            @foreach($errors->all() as $e)
                            <div style="font-size:13px;"><i class="mdi mdi-alert-circle mr-1"></i>{{ $e }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('blog-category.update', $category->id) }}" method="POST">
                            @csrf
                            <div style="margin-bottom:18px;">
                                <label class="blog-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="blog-input"
                                    value="{{ old('title', $category->title) }}" required>
                            </div>
                            <div style="margin-bottom:20px;">
                                <label class="blog-label">Description</label>
                                <textarea name="description" class="blog-input" rows="3">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <div class="d-flex" style="gap:8px;">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="mdi mdi-check mr-1"></i> Update
                                </button>
                                <a href="{{ route('blog-category.index') }}" class="btn btn-light flex-fill">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- All Categories --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <p class="section-title">All Categories</p>

                        @if($categories->count())
                        <div class="table-responsive">
                            <table class="table table-hover" style="font-size:14px;">
                                <thead style="background:#f8f9fa;">
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th style="width:90px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $cat)
                                    <tr style="{{ $cat->id == $category->id ? 'background:#f0faf7;' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width:30px;height:30px;background:{{ $cat->id == $category->id ? '#28bf96' : '#e8f8f3' }};border-radius:50%;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                                                    <i class="mdi mdi-tag" style="color:{{ $cat->id == $category->id ? '#fff' : '#28bf96' }};font-size:14px;"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $cat->title }}</strong>
                                                    @if($cat->id == $category->id)
                                                    <span style="background:#28bf96;color:#fff;font-size:10px;border-radius:20px;padding:1px 7px;margin-left:5px;">Editing</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td><code style="font-size:12px;background:#f4f4f4;padding:2px 7px;border-radius:4px;color:#555;">{{ $cat->slug }}</code></td>
                                        <td><span class="text-muted">{{ Str::limit($cat->description, 35) ?: '—' }}</span></td>
                                        <td>
                                            @if($cat->id != $category->id)
                                            <a href="{{ route('blog-category.edit', $cat->id) }}" class="btn btn-sm btn-info mr-1">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('blog-deleteCat') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $cat->id }}">
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $categories->links() }}</div>
                        @else
                        <div class="text-center py-4">
                            <p class="text-muted">No categories found.</p>
                        </div>
                        @endif
                    </div>
                </div>
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
            title: 'Delete Category?', text: "This cannot be undone.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#fc5a5a', cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete!'
        }).then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endsection
