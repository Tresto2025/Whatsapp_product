@extends('layouts.admin', ['activePage' => 'all-doctors', 'titlePage' => __('All Doctors')])
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Edit Post</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog-post.index') }}">Posts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Post Details</h4>
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('blog-post.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="forms-sample">
                            @csrf
                            
                            <div class="form-group">
                                <label for="title">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="content">Content *</label>
                                <textarea class="form-control" id="content" name="content" rows="10">{{ old('content', $post->content) }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="excerpt">Excerpt</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="categories">Category</label>
                                        <select class="form-control" id="categories" name="categories">
                                            <option value="">Select Category</option>
                                            @foreach($categories ?? [] as $category)
                                                <option value="{{ $category->title }}" {{ old('categories', $post->categories) == $category->title ? 'selected' : '' }}>{{ $category->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="thumbnail">Thumbnail Image</label>
                                @if($post->thumbnail)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="Current thumbnail" style="max-width: 200px; height: auto;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep current thumbnail</small>
                            </div>
                            
                            <hr>
                            <h5>SEO Settings</h5>
                            
                            <div class="form-group">
                                <label for="seo_title">SEO Title</label>
                                <input type="text" class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title', $post->seo_title) }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="keyword">Keywords</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" value="{{ old('keyword', $post->keyword) }}" placeholder="Comma separated keywords">
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Meta Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $post->description) }}</textarea>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary mr-2">Update Post</button>
                                <a href="{{ route('blog-post.index') }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>
    <script>
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('textarea'))
            .then(editor => {
                console.log('Editor was initialized', editor);
            })
            .catch(error => {
                console.error('Error during initialization of the editor', error);
            });
    </script>
@endpush

