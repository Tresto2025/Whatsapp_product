@extends('layouts.admin', ['activePage' => 'all-doctors', 'titlePage' => __('All Doctors')])
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">Create New Post</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blog-post.index') }}">Posts</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Post Details</h4>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('blog-post.store') }}" method="POST" enctype="multipart/form-data"
                                class="forms-sample">
                                @csrf

                                <div class="form-group">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="content" class="mb-3">Content *</label>
                                    <textarea class="form-control" id="content" name="content" rows="15">{{ old('content') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="excerpt">Excerpt</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="categories">Category</label>
                                            <select class="form-control" id="categories" name="categories">
                                                <option value="">Select Category</option>
                                                @foreach($categories ?? [] as $category)
                                                    <option value="{{ $category->title }}" {{ old('categories') == $category->title ? 'selected' : '' }}>{{ $category->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status *</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                                                    Draft</option>
                                                <option value="published"
                                                    {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="thumbnail">Thumbnail Image</label>
                                    <input type="file" class="form-control" id="thumbnail" name="thumbnail"
                                        accept="image/*">
                                </div>

                                <hr>
                                <h5>SEO Settings</h5>

                                <div class="form-group">
                                    <label for="seo_title">SEO Title</label>
                                    <input type="text" class="form-control" id="seo_title" name="seo_title"
                                        value="{{ old('seo_title') }}">
                                </div>

                                <div class="form-group">
                                    <label for="keyword">Keywords</label>
                                    <input type="text" class="form-control" id="keyword" name="keyword"
                                        value="{{ old('keyword') }}" placeholder="Comma separated keywords">
                                </div>

                                <div class="form-group">
                                    <label for="description">Meta Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary mr-2">Create Post</button>
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
