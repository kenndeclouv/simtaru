@extends('layouts.app')
@section('title', 'Template Dokumen')

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
                    <div class="d-flex align-items-center">
                        <div class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Bantuan">
                            <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#modalHelp">
                                <i class="fa-solid fa-info-circle"></i>
                            </button>

                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalUploadTemplate">
                            Tambahkan Template
                        </button>
                    </div>
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
                                    @can('view template')
                                        <a href="{{ route('template.show', $template->id) }}" class="btn btn-sm btn-info"
                                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endcan
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
        <!-- Help modal -->
        <div class="modal fade" id="modalHelp" tabindex="-1" aria-labelledby="modalHelpLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHelpLabel">Bantuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p>Silahkan upload file template dokumen yang akan digunakan untuk membuat dokumen.</p>
                        <p>Placeholder adalah variabel yang akan diganti dengan data yang diinputkan.</p>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Placeholder</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${var_nik}</td>
                                    <td>NIK pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_nama}</td>
                                    <td>Nama pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${text_alamat}</td>
                                    <td>Alamat lengkap pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_provinsi}</td>
                                    <td>Provinsi domisili pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_kabupaten}</td>
                                    <td>Kabupaten/Kota domisili pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_kecamatan}</td>
                                    <td>Kecamatan domisili pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_kelurahan}</td>
                                    <td>Kelurahan/Desa domisili pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_email}</td>
                                    <td>Email pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_no_telp}</td>
                                    <td>Nomor telepon pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_no_ponsel}</td>
                                    <td>Nomor ponsel pemohon.</td>
                                </tr>
                                <tr>
                                    <td>${var_nama_usaha}</td>
                                    <td>Nama usaha yang diajukan.</td>
                                </tr>
                                <tr>
                                    <td>${var_bentuk_usaha}</td>
                                    <td>Bentuk usaha yang diajukan.</td>
                                </tr>
                                <tr>
                                    <td>${text_alamat_usaha}</td>
                                    <td>Alamat lengkap usaha.</td>
                                </tr>
                                <tr>
                                    <td>${var_kecamatan_usaha}</td>
                                    <td>Kecamatan lokasi usaha.</td>
                                </tr>
                                <tr>
                                    <td>${var_kelurahan_usaha}</td>
                                    <td>Kelurahan/Desa lokasi usaha.</td>
                                </tr>
                                <tr>
                                    <td>${var_rencana_usaha}</td>
                                    <td>Rencana usaha yang akan dijalankan.</td>
                                </tr>
                                <tr>
                                    <td>${dec_rencana_luas_lantai}</td>
                                    <td>Luas lantai rencana usaha (m<sup>2</sup>).</td>
                                </tr>
                                <tr>
                                    <td>${json_geometry}</td>
                                    <td>Data geometri lokasi usaha (format JSON).</td>
                                </tr>
                                <tr>
                                    <td>${var_gambar_area}</td>
                                    <td>Gambar area lokasi usaha.</td>
                                </tr>
                                <tr>
                                    <td>${var_nomor_permohonan}</td>
                                    <td>Nomor permohonan.</td>
                                </tr>
                                <tr>
                                    <td>${date_tanggal_permohonan}</td>
                                    <td>Tanggal permohonan diajukan.</td>
                                </tr>
                                <tr>
                                    <td>${var_nomor_pengesahan}</td>
                                    <td>Nomor pengesahan permohonan.</td>
                                </tr>
                                <tr>
                                    <td>${date_tanggal_pengesahan}</td>
                                    <td>Tanggal pengesahan permohonan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
