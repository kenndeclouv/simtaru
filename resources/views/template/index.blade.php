@extends('layouts.app')
@section('title', 'Data Santri')

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                dropdownParent: $('.select2').parent(),
                placeholder: 'Pilih Jenis Dokumen'
            });
            $('.datatable').DataTable({
                scrollY: "300px",
                scrollX: !0,
                layout: {
                    topStart: {
                        rowClass: "row mx-3 my-0 justify-content-between",
                        features: [{
                            pageLength: {
                                menu: [7, 10, 25, 50, 100],
                                text: "Show_MENU_entries"
                            }
                        }]
                    },
                    topEnd: {
                        search: {
                            placeholder: ""
                        }
                    },
                    bottomStart: {
                        rowClass: "row mx-3 justify-content-between",
                        features: ["info"]
                    },
                    bottomEnd: {
                        paging: {
                            firstLast: !1
                        }
                    }
                },
            });
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5
                class="card-header text-md-start text-center border-bottom d-flex justify-content-between align-items-center">
                Daftar Template Dokumen
                @can('create template')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadTemplate">
                        Tambahkan Template
                    </button>
                @endcan
            </h5>
            <div class="card-body">
                <div id="upload-result"></div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatable table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Nama Template</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $template)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $template->enum_jenis }}</td>
                                <td>{{ $template->var_nama }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $template->var_file_path) }}" target="_blank">
                                        {{ basename($template->var_file_path) }}
                                    </a>
                                </td>
                                <td>
                                    @can('edit template')
                                        <a href="{{ route('template.edit', $template->id) }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endcan
                                    @can('delete template')
                                        <x-delete :route="route('template.destroy', $template->id)" :title="'Hapus Template'" />
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Upload Template -->
        <div class="modal fade" id="modalUploadTemplate" tabindex="-1" aria-labelledby="modalUploadTemplateLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="upload-template-form" action="{{ route('template.store') }}" method="POST"
                    enctype="multipart/form-data" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUploadTemplateLabel">Upload Template Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="templateName" class="form-label">Nama Template</label>
                            <input type="text" class="form-control" id="templateName" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="templateFile" class="form-label">File Template (.docx)</label>
                            <input type="file" class="form-control" id="templateFile" name="template" accept=".docx"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="templateFile" class="form-label">Jenis Dokumen</label>
                            <select name="jenis" id="jenis" class="form-select select2" required>
                                <option value="" disabled selected>Pilih Jenis Dokumen</option>
                                <option value="sitr">SITR</option>
                                <option value="rdtr">RDTR</option>
                                <option value="kkpr">KKPR</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
