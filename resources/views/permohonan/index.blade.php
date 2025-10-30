@extends('layouts.app')
@section('title', 'Data Permohonan SITR')

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(e) {
            let type = new URLSearchParams(window.location.search).get('type');
            $('.select2').select2({
                dropdownParent: '#statusModal',
                placeholder: "Pilih"
            });

            let tableEl = document.querySelector(".dt-scrollableTable");
            tableEl && new DataTable(tableEl, {
                processing: !0,
                serverSide: !0,
                ajax: "{{ route('permohonan.index') }}?type=" + type,
                columns: [{
                        data: 'var_nama',
                        title: 'Nama'
                    },
                    @if ($type == 'sitr/rdtr')
                        {
                            data: 'var_nik',
                            title: 'NIK'
                        }, {
                            data: 'var_kabupaten',
                            title: 'Kabupaten'
                        }, {
                            data: 'var_rencana_usaha',
                            title: 'Rencana Usaha'
                        },
                    @endif
                    {
                        data: 'date_tanggal_permohonan',
                        title: 'Tgl Permohonan',
                    },
                    {
                        data: 'enum_status',
                        title: 'Status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return data === 'approved' ?
                                '<span class="badge bg-label-success">Diterima & telah di TTE</span>' :
                                data === 'pending' ?
                                '<span class="badge bg-label-warning">Diproses</span>' :
                                data === 'request_tte' ?
                                '<span class="badge bg-label-warning">Proses TTE</span>' :
                                '<span class="badge bg-label-danger">Ditolak</span>';
                        }
                    },
                    {
                        data: null,
                        title: 'Aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var showUrl = `/permohonan/${row.id}?type={{ $type }}`;
                            var editUrl = `/permohonan/${row.id}/edit?type={{ $type }}`;
                            var deleteUrl = `/permohonan/${row.id}?type={{ $type }}`;
                            var namaPengusul = row.var_nama;

                            let buttons = `
                                <div class="d-flex align-items-center">
                                    <a href="${showUrl}" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Lihat Permohonan"><i class="fas fa-eye"></i></a>
                            `;

                            @can('edit permohonan')
                                if (row.enum_status !== 'approved') {
                                    buttons += `
                                        <div data-bs-toggle="tooltip" title="Ubah Status Permohonan">
                                            <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#statusModal" data-id="${row.id}" data-status="${row.enum_status}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    `;
                                }
                                buttons += `
                                    <a href="${editUrl}" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Edit Permohonan"><i class="fas fa-pen-to-square"></i></a>
                                `;
                            @endcan

                            @can('delete permohonan')
                                buttons += `
                                    <a href="javascript:;" class="btn btn-sm btn-danger btn-delete" data-url="${deleteUrl}" data-name="${namaPengusul}" data-bs-toggle="tooltip" title="Hapus Permohonan">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                `;
                            @endcan

                            if (row.enum_status !== 'pending') {
                                buttons += `
                                    <div class="btn-group ms-1">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Tindakan">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            ${
                                                row.permohonan_template_docs && row.permohonan_template_docs.length > 0 && row.permohonan_template_docs[0].var_generated_file_path
                                                ? `
                                                    <li>
                                                        <button
                                                            class="dropdown-item btn-preview-doc"
                                                            data-doc-url="${row.permohonan_template_docs[0].var_generated_file_path}"
                                                            type="button"
                                                        >
                                                            <i class="fas fa-file-word me-2"></i>Preview (.docx)
                                                        </button>
                                                    </li>
                                                `
                                                : `
                                                    <li>
                                                        <span class="dropdown-item text-muted" style="cursor:not-allowed;"><i class="fas fa-file-word me-2"></i>Preview - belum dibuat</span>
                                                    </li>
                                                `
                                            }
                                            ${row.enum_status === 'approved' ? `
                                                <li>
                                                    <a class="dropdown-item" href="${row.permohonan_template_docs[0].var_generated_file_path}" target="_blank">
                                                        <i class="fas fa-signature me-2"></i>View TTE
                                                    </a>
                                                </li>
                                            ` : ''}
                                        </ul>
                                    </div>
                                `;
                            }
                            buttons += `</div>`;
                            return buttons;
                        }
                    }
                ],
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
                drawCallback: function(settings) {
                    // Cari semua elemen tooltip yang baru digambar di dalam tabel
                    const tooltipTriggerList = [].slice.call(this.api().table().body().querySelectorAll(
                        '[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },
            });

            // Status modal
            const statusModal = document.getElementById('statusModal');
            statusModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const permohonanId = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-status');
                const formAction = `/permohonan/${permohonanId}/status`;
                const form = statusModal.querySelector('form');
                form.setAttribute('action', formAction);
                const statusSelect = statusModal.querySelector('#status');
                $(statusSelect).val(currentStatus).trigger('change');
            });

            // Delete handler
            $('.dt-scrollableTable').on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).data('url');
                const itemName = $(this).data('name');
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `Data permohonan atas nama "${itemName}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.action = deleteUrl;
                        form.method = 'POST';
                        form.style.display = 'none';
                        let csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);
                        let methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Handle preview docx modal
            $(document).on('click', '.btn-preview-doc', function() {
                const docUrl = $(this).data('doc-url');
                const $modal = $('#previewModal');
                const $container = $('#modal-preview-content');
                $container.html('<p class="text-center"><em>Loading preview...</em></p>');
                $modal.modal('show');
                fetch(docUrl)
                    .then(response => response.blob())
                    .then(blob => {
                        docx.renderAsync(blob, $container[0])
                            .then(x => {})
                            .catch(e => $container.html('<p class="text-center text-danger">Gagal memuat file preview.</p>'));
                    }).catch(e => {
                        $container.html('<p class="text-center text-danger">Gagal memuat file preview.</p>');
                    });
            });

        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Permohonan ' . strtoupper($type)]]" />

        <!-- Scrollable -->
        <div class="card">
            <div class="card-body d-block d-lg-flex border-bottom">
                <h5 class="text-start">Permohonan {{ strtoupper($type) }}</h5>
                @can('create permohonan')
                    <a href="{{ route('permohonan.create') }}?type={{ $type }}"
                        class="btn btn-primary ms-0 ms-lg-auto">Tambahkan Permohonan</a>
                @endcan
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-scrollableTable table table-stripped table-responsive">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            @if ($type == 'sitr/rdtr')
                                <th>Kabupaten</th>
                                <th>Rencana Usaha</th>
                                <th>Tanggal Permohonan</th>
                            @endif
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Ubah Status Permohonan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="statusForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select select2">
                                <option value="" select>Pilih Status</option>
                                <option value="request_tte">Verifikasi & Pengajuan TTE</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lampiran" class="form-label">Lampiran</label>
                            <input type="file" name="lampiran" id="lampiran" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Ubah Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview DOCX --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header mb-4">
                    <h5 class="modal-title" id="previewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-preview-content" class="overflow-hidden rounded" style="min-height: 400px;"></div>
                </div>
                <div class="modal-footer mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
