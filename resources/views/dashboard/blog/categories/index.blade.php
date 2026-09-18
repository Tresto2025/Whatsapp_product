@extends('layouts.admin', ['activePage' => 'blog-categories', 'titlePage' => 'Blog Categories'])
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
            <h3 class="page-title"><i class="mdi mdi-tag-multiple mr-2" style="color:#28bf96;"></i>Blog Categories</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categories</li>
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

            {{-- Add Form --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <p class="section-title">Add New Category</p>

                        @if($errors->any())
                        <div class="alert alert-danger py-2 mb-3">
                            @foreach($errors->all() as $e)
                            <div style="font-size:13px;"><i class="mdi mdi-alert-circle mr-1"></i>{{ $e }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('blog-category.store') }}" method="POST">
                            @csrf
                            <div style="margin-bottom:18px;">
                                <label class="blog-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="blog-input" value="{{ old('title') }}"
                                    placeholder="e.g. Health Tips" required>
                            </div>
                            <div style="margin-bottom:20px;">
                                <label class="blog-label">Description</label>
                                <textarea name="description" class="blog-input" rows="3"
                                    placeholder="Short description...">{{ old('description') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="mdi mdi-plus mr-1"></i> Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <p class="section-title mb-0" style="border:none;padding:0;">All Categories</p>
                            <span style="background:#e8f8f3;color:#28bf96;padding:4px 12px;border-radius:20px;font-size:13px;font-weight:600;">
                                {{ $categories->total() }} total
                            </span>
                        </div>

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
                                    @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width:30px;height:30px;background:#e8f8f3;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                                                    <i class="mdi mdi-tag" style="color:#28bf96;font-size:14px;"></i>
                                                </div>
                                                <strong>{{ $category->title }}</strong>
                                            </div>
                                        </td>
                                        <td><code style="font-size:12px;background:#f4f4f4;padding:2px 7px;border-radius:4px;color:#555;">{{ $category->slug }}</code></td>
                                        <td><span class="text-muted">{{ Str::limit($category->description, 40) ?: '—' }}</span></td>
                                        <td>
                                            <a href="{{ route('blog-category.edit', $category->id) }}" class="btn btn-sm btn-info mr-1">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('blog-deleteCat') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $category->id }}">
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $categories->links() }}</div>
                        @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-tag-outline" style="font-size:48px;color:#ddd;"></i>
                            <p class="text-muted mt-2 mb-0">No categories yet.</p>
                            <small class="text-muted">Use the form on the left to add one.</small>
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
