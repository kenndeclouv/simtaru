@extends('layouts.app')
@section('title', 'Data Permohonan SITR')

@section('page-script')
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
                            var showUrl = "{{ route('permohonan.show', ['permohonan' => ':id', 'type' => $type]) }}".replace(':id', row.id);
                            var editUrl = "{{ route('permohonan.edit', ['permohonan' => ':id', 'type' => $type]) }}".replace(':id', row.id);
                            var deleteUrl = "{{ route('permohonan.destroy', ['permohonan' => ':id', 'type' => $type]) }}".replace(':id', row.id);
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
                                // Ambil data dokumen pertama (kalo ada)
                                let firstDoc = row.permohonan_template_docs && row.permohonan_template_docs.length > 0 ? row.permohonan_template_docs[0] : null;
                                // Coba ambil nama dokumennya juga biar keren pas di modal
                                let docName = firstDoc && firstDoc.template_docs ? firstDoc.template_docs.var_nama : 'Dokumen';

                                buttons += `
                                    <div class="btn-group ms-1">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Tindakan">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            ${
                                                firstDoc && firstDoc.var_generated_file_path
                                                ? `
                                                    <li>
                                                        <button
                                                            class="dropdown-item btn-preview-doc"
                                                            data-file-url="${firstDoc.var_generated_file_path}"
                                                            data-file-name="${docName}"
                                                            type="button"
                                                        >
                                                            <i class="fas fa-file-pdf me-2"></i>Preview PDF
                                                        </button>
                                                    </li>
                                                `
                                                : `
                                                    <li>
                                                        <span class="dropdown-item text-muted" style="cursor:not-allowed;"><i class="fas fa-file-pdf me-2"></i>Preview - belum dibuat</span>
                                                    </li>
                                                `
                                            }
                                            ${row.enum_status === 'approved' && firstDoc ? `
                                                <li>
                                                    <a class="dropdown-item" href="${firstDoc.var_generated_file_path}" target="_blank">
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
                const formAction = "{{ route('permohonan.status', ['permohonan' => ':id', 'type' => $type]) }}".replace(':id', permohonanId);
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

            $(document).on('click', '.btn-preview-doc', function() {
                const fileUrl = $(this).data('file-url');
                const fileName = $(this).data('file-name');

                const $modal = $('#previewModal');
                const $modalTitle = $('#previewModalLabel');
                const $container = $('#modal-preview-content');

                // Update judul modal
                $modalTitle.text('Preview: ' + (fileName || 'Dokumen'));

                // Tampilin loading dulu
                $container.html(`
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);

                $modal.modal('show');

                setTimeout(() => {
                    $container.html(`
                        <iframe src="${fileUrl}" width="100%" height="100%" style="border: none;" allowfullscreen>
                            <p>Browser kamu tidak mendukung preview PDF.
                            <a href="${fileUrl}" target="_blank">Download file</a> sebagai gantinya.</p>
                        </iframe>
                    `);
                }, 300);
            });

            $('#previewModal').on('hidden.bs.modal', function () {
                $('#modal-preview-content').empty();
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

    {{-- MODAL PREVIEW PDF (Sama persis kayak show.blade.php) --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <div id="modal-preview-content" class="h-100 w-100">
                        {{-- Iframe akan dimuat di sini --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
