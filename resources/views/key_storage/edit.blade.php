@extends('layouts.app')

@section('title', 'Edit Key Storage')

@section('page-script')
    <script>
        $('.select2').select2();
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Key Storage', 'url' => route('key-storages.index')], ['text' => 'Edit Key Storage']]" />

        <form action="{{ route('key-storages.update', $keyStorage->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-header border-bottom mb-3">
                    <h5>Edit Key Storage</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="var_key" class="form-label">Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('var_key') is-invalid @enderror" id="var_key"
                            name="var_key" value="{{ old('var_key', $keyStorage->var_key) }}" required>
                        @error('var_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="var_value" class="form-label">Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('var_value') is-invalid @enderror" id="var_value"
                            name="var_value" value="{{ old('var_value', $keyStorage->var_value) }}" required>
                        @error('var_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="var_description" class="form-label">Description</label>
                        <input type="text" class="form-control @error('var_description') is-invalid @enderror"
                            id="var_description" name="var_description"
                            value="{{ old('var_description', $keyStorage->var_description) }}">
                        @error('var_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Ubah</button>
                    <a href="{{ route('key-storages.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
@endsection
