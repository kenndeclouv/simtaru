@extends('layouts.app')
@section('title', 'Detail Template')

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.js"></script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Template', 'url' => route('template.index')], ['text' => 'Detail']]" />

        <div class="card">
            <div class="card-header border-bottom mb-3">
                <h5 class="mb-0">Detail Template</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12 col-lg-8">
                        <h5 class="fw-bold"># Informasi Pengusul</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Nama</dt>
                            <dd class="col-sm-8">: {{ $template->var_nama }}</dd>

                            <dt class="col-sm-4">Jenis</dt>
                            <dd class="col-sm-8">: {{ $template->enum_jenis }}</dd>

                            <dt class="col-sm-4">File</dt>
                            <dd class="col-sm-8">: <a href="{{ asset('storage/' . $template->var_file_path) }}"
                                    target="_blank">Download file</a></dd>
                        </dl>
                        <h5 class="fw-bold mt-4"># Placeholders</h5>
                        <ul class="">
                            @foreach ($placeholders as $placeholder)
                                <li class="">{{ $placeholder->var_key }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold"># Preview</h5>
                        <div id="preview-container" class="border p-3 rounded" style="min-height: 400px;">
                            Loading preview...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil URL file dari blade
            const fileUrl = "{{ asset('storage/' . $template->var_file_path) }}";
            const previewContainer = document.getElementById("preview-container");

            // Ambil file dari server
            fetch(fileUrl)
                .then(response => response.blob()) // Ubah response jadi blob
                .then(blob => {
                    // Render file docx ke dalam div #preview-container
                    docx.renderAsync(blob, previewContainer)
                        .then(x => console.log("docx: Privew berhasil dirender."));
                })
                .catch(error => {
                    console.error('Error fetching or rendering DOCX:', error);
                    previewContainer.innerText = "Gagal memuat preview file.";
                });
        });
    </script>
@endsection
