@extends('layouts.app')

@section('title', 'Edit Template')

@section('page-script')

@endsection

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Template Dokumen', 'url' => route('template.index')], ['text' => 'Edit Template']]" />
        <form action="{{ route('template.update', $template->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Edit Template Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Template</label>
                        <input type="text" name="nama" id="nama" class="form-control"
                            value="{{ old('nama', $template->var_nama) }}" required>
                        @error('nama')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="template" class="form-label">File Template (.docx)</label>
                        <input type="file" name="template" id="template" class="form-control" accept=".docx">
                        @if ($template->var_file_path)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $template->var_file_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-secondary">
                                    Lihat File Lama
                                </a>
                            </div>
                        @endif
                        @error('template')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Dokumen</label>
                        <select name="jenis" id="jenis" class="form-select" required>
                            <option value="" disabled {{ old('jenis', $template->enum_jenis) ? '' : 'selected' }}>
                                Pilih Jenis Dokumen</option>
                            <option value="sitr" {{ old('jenis', $template->enum_jenis) == 'sitr' ? 'selected' : '' }}>
                                SITR</option>
                            <option value="rdtr" {{ old('jenis', $template->enum_jenis) == 'rdtr' ? 'selected' : '' }}>
                                RDTR</option>
                            <option value="kkpr" {{ old('jenis', $template->enum_jenis) == 'kkpr' ? 'selected' : '' }}>
                                KKPR</option>
                        </select>
                        @error('jenis')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Template</button>
        </form>
    </div>
@endsection
