@extends('layouts.admin', ['activePage' => 'all-doctors', 'titlePage' => __('All Doctors')])
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Edit Category</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog-category.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        
        <div class="row">
            <div class="col-lg-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Category</h4>
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('blog-category.update', $category->id) }}" method="POST" class="forms-sample">
                            @csrf
                            
                            <div class="form-group">
                                <label for="title">Category Name *</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $category->title) }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mr-2">Update Category</button>
                            <a href="{{ route('blog-category.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">All Categories</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $cat)
                                    <tr class="{{ $cat->id == $category->id ? 'table-active' : '' }}">
                                        <td><strong>{{ $cat->title }}</strong></td>
                                        <td><small class="text-muted">{{ $cat->slug }}</small></td>
                                        <td>{{ Str::limit($cat->description, 50) }}</td>
                                        <td>
                                            @if($cat->id != $category->id)
                                            <a href="{{ route('blog-category.edit', $cat->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('blog-deleteCat') }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $cat->id }}">
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="badge badge-primary">Editing</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No categories found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $categories->links() }}
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
            text: "Do you want to delete this category?",
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
