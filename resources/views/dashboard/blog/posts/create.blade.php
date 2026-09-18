@extends('layouts.admin', ['activePage' => 'blog-posts', 'titlePage' => 'Create Post'])
@section('header-link')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .blog-input {
            display: block !important;
            width: 100% !important;
            padding: 10px 14px !important;
            font-size: 14px !important;
            border: 1px solid #ced4da !important;
            border-radius: 6px !important;
            background: #fff !important;
            background-image: none !important;
            color: #253237 !important;
            transition: border-color .15s ease-in-out !important;
        }

        .blog-input:focus {
            border-color: #28bf96 !important;
            outline: 0 !important;
            box-shadow: 0 0 0 3px rgba(40, 191, 150, .12) !important;
            background-image: none !important;
        }

        .blog-input-lg {
            font-size: 16px !important;
            padding: 12px 14px !important;
            font-weight: 500 !important;
        }

        .blog-label {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #253237 !important;
            margin-bottom: 8px !important;
            margin-top: 0 !important;
            display: block !important;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #28bf96;
            padding-bottom: 8px;
            border-bottom: 2px solid #e8f8f3;
            margin-bottom: 20px;
        }

        .sidebar-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 16px;
            background: #fff;
        }

        .thumb-drop {
            width: 100%;
            height: 130px;
            background: #f8fffe;
            border: 2px dashed #28bf96;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s;
        }

        .thumb-drop:hover {
            background: #e8f8f3;
        }

        .note-editor.note-frame {
            border: 1px solid #ced4da !important;
            border-radius: 6px !important;
        }

        .note-toolbar {
            background: #f8f9fa !important;
            border-bottom: 1px solid #ced4da !important;
            border-radius: 6px 6px 0 0 !important;
        }
    </style>
@endsection
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">

            <div class="page-header">
                <h3 class="page-title"><i class="mdi mdi-plus-circle mr-2" style="color:#28bf96;"></i>Create New Post</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blog-post.index') }}">Posts</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle mr-1"></i>
                    @foreach ($errors->all() as $e)
                        {{ $e }}.
                    @endforeach
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <form action="{{ route('blog-post.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
                @csrf
                <div class="row">

                    {{-- LEFT: Main Content --}}
                    <div class="col-lg-8">

                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="section-title">Post Title</p>
                                <input type="text" name="title" class="blog-input blog-input-lg"
                                    value="{{ old('title') }}" placeholder="Enter post title here..." required>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="section-title">Content</p>
                                <textarea id="content" name="content">{{ old('content') }}</textarea>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="section-title">Excerpt <span
                                        style="font-weight:400;text-transform:none;font-size:12px;color:#999;">— short
                                        summary shown in listings</span></p>
                                <textarea name="excerpt" class="blog-input" rows="3" placeholder="Write a short summary...">{{ old('excerpt') }}</textarea>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="section-title">SEO Settings</p>
                                <div style="margin-bottom:18px;">
                                    <label class="blog-label">SEO Title</label>
                                    <input type="text" name="seo_title" class="blog-input" value="{{ old('seo_title') }}"
                                        placeholder="SEO optimized title">
                                </div>
                                <div style="margin-bottom:18px;">
                                    <label class="blog-label">Keywords</label>
                                    <input type="text" name="keyword" class="blog-input" value="{{ old('keyword') }}"
                                        placeholder="health, doctor, tips">
                                </div>
                                <div>
                                    <label class="blog-label">Meta Description</label>
                                    <textarea name="description" class="blog-input" rows="3" placeholder="Short description for search engines...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT: Sidebar --}}
                    <div class="col-lg-4">

                        <div class="sidebar-card">
                            <p class="section-title">Publish</p>
                            <label class="blog-label">Status</label>
                            <select name="status" class="blog-input" required>
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>📝 Draft
                                </option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>✅ Published
                                </option>
                            </select>
                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('blog-post.index') }}" class="btn btn-light btn-sm">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    <i class="mdi mdi-check mr-1"></i> Publish
                                </button>
                            </div>
                        </div>

                        <div class="sidebar-card">
                            <p class="section-title">Category</p>
                            <select name="category_id" class="blog-input">
                                <option value="">— No Category —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($categories->isEmpty())
                                <a href="{{ route('blog-category.index') }}" class="d-block mt-2"
                                    style="font-size:12px;color:#28bf96;">
                                    <i class="mdi mdi-plus"></i> Create a category first
                                </a>
                            @endif
                        </div>

                        <div class="sidebar-card">
                            <p class="section-title">Thumbnail Image</p>
                            <div id="thumb-preview" style="display:none;margin-bottom:10px;">
                                <img id="preview-img" src=""
                                    style="width:100%;height:160px;object-fit:cover;border-radius:6px;border:1px solid #e9ecef;">
                                <button type="button" onclick="clearThumb()"
                                    style="font-size:11px;color:#fc5a5a;background:none;border:none;padding:4px 0;cursor:pointer;">
                                    <i class="mdi mdi-close"></i> Remove
                                </button>
                            </div>
                            <div class="thumb-drop" id="thumb-drop"
                                onclick="document.getElementById('thumbnail').click()">
                                <div class="text-center">
                                    <i class="mdi mdi-image-plus" style="font-size:32px;color:#28bf96;"></i>
                                    <p style="font-size:12px;color:#28bf96;margin:6px 0 0;">Click to upload</p>
                                    <p style="font-size:11px;color:#aaa;margin:2px 0 0;">JPG, PNG — Max 2MB</p>
                                </div>
                            </div>
                            <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                                style="display:none;" onchange="previewThumb(this)">
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log('jQuery loaded:', typeof jQuery !== 'undefined');
            console.log('Summernote loaded:', typeof $.fn.summernote !== 'undefined');

            // Initialize Summernote with image upload
            $('#content').summernote({
                height: 400,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0]);
                    }
                }
            });

            console.log('Summernote initialized');
        });

        // function uploadImage(file) {
        //     var formData = new FormData();
        //     formData.append('upload', file);
        //     formData.append('_token', '{{ csrf_token() }}');

        //     $.ajax({
        //         url: '{{ route('blog-post.upload-image') }}',
        //         method: 'POST',
        //         data: formData,
        //         contentType: false,
        //         processData: false,
        //         success: function(response) {
        //             $('#content').summernote('insertImage', response.url);
        //         },
        //         error: function(xhr) {
        //             var message = 'Image upload failed.';
        //             if (xhr.responseJSON && xhr.responseJSON.message) {
        //                 message = xhr.responseJSON.message;
        //             }
        //             alert(message);
        //         }
        //     });
        // }

        // function previewThumb(input) {
        //     if (input.files && input.files[0]) {
        //         var reader = new FileReader();
        //         reader.onload = function(e) {
        //             document.getElementById('preview-img').src = e.target.result;
        //             document.getElementById('thumb-preview').style.display = 'block';
        //             document.getElementById('thumb-drop').style.display = 'none';
        //         };
        //         reader.readAsDataURL(input.files[0]);
        //     }
        // }

        // function clearThumb() {
        //     document.getElementById('thumbnail').value = '';
        //     document.getElementById('thumb-preview').style.display = 'none';
        //     document.getElementById('thumb-drop').style.display = 'flex';
        // }
        
             function uploadImage(file) {
            var formData = new FormData();
            formData.append('upload', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('blog-post.upload-image') }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('Upload success:', response);
                    if (response.url) {
                        $('#content').summernote('insertImage', response.url);
                        console.log('Image inserted:', response.url);
                    } else {
                        console.error('No URL in response');
                        alert('Image uploaded but no URL returned');
                    }
                },
                error: function(xhr) {
                    console.error('Upload error:', xhr);
                    var message = 'Image upload failed.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }

        function previewThumb(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('thumb-preview').style.display = 'block';
                    document.getElementById('thumb-drop').style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearThumb() {
            document.getElementById('thumbnail').value = '';
            document.getElementById('thumb-preview').style.display = 'none';
            document.getElementById('thumb-drop').style.display = 'flex';
        }
    </script>
@endpush
