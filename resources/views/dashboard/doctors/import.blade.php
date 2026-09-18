@extends('layouts.admin', ['activePage' => 'all-doctors', 'titlePage' => __('Import Doctors')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Import Doctors</h4>

                        {{-- Success Message --}}
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Error Message --}}
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                            <ul>
                                @foreach(session('details') ?? [] as $failure)
                                    <li>
                                        Row {{ $failure->row() }} :
                                        {{ implode(', ', $failure->errors()) }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <form class="form-sample" action="{{ route('doctors.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Upload CSV/Excel File</label>
                                        <input type="file" class="form-control" name="csv_file" accept=".csv,.xlsx" required>
                                        @if ($errors->has('csv_file'))
                                            <span class="text-danger">{{ $errors->first('csv_file') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-primary">Import</button>
                                        <a href="{{ url('sample-doctors.csv') }}" class="btn btn-outline-secondary">Download Sample CSV</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
