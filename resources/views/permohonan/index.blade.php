@extends('layouts.app')
@section('title', 'Data Permohonan SITR')

@section('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function(e) {
            let a = document.querySelector(".dt-scrollableTable");
            a && new DataTable(a, {
                processing: !0,
                serverSide: !0,
                ajax: "{{ route('permohonan.index') }}",
                columns: [{
                        data: 'var_nama',
                        title: 'Nama'
                    },
                    {
                        data: 'var_nik',
                        title: 'NIK'
                    },
                    {
                        data: 'var_kabupaten',
                        title: 'Kabupaten'
                    },
                    {
                        data: 'var_rencana_usaha',
                        title: 'Rencana Usaha'
                    },
                    {
                        data: 'date_tanggal_permohonan',
                        title: 'Tgl Permohonan',
                    },
                    {
                        data: 'var_nomor_pengesahan',
                        title: 'Status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return data ?
                                '<span class="badge bg-label-success">Selesai</span>' :
                                '<span class="badge bg-label-warning">Diproses</span>';
                        }
                    },
                    {
                        data: null,
                        title: 'Aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var showUrl = '/permohonan/' + row.id;
                            var editUrl = '/permohonan/' + row.id + '/edit';

                            // --- PERUBAHAN DI SINI ---
                            var deleteUrl = '/permohonan/' + row.id;
                            // Kita simpan nama pengusul untuk ditampilkan di Swal
                            var namaPengusul = row.var_nama;

                            return `
                                <div class="d-flex align-items-center">
                                    <a href="${showUrl}" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Lihat Permohonan">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @can('edit permohonan')
                                    <a href="${editUrl}" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Edit Permohonan">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    @endcan

                                    @can('delete permohonan')
                                    <a href="javascript:;" class="btn btn-sm btn-danger btn-delete" 
                                    data-url="${deleteUrl}" 
                                    data-name="${namaPengusul}" 
                                    data-bs-toggle="tooltip" title="Hapus Permohonan">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                    @endcan
                                </div>
                            `;
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

                    // Inisialisasi setiap tooltip
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },
            });
            $('.dt-scrollableTable').on('click', '.btn-delete', function(e) {
                e.preventDefault(); // Mencegah aksi default link

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
                        // Buat form dinamis untuk mengirim request DELETE
                        let form = document.createElement('form');
                        form.action = deleteUrl;
                        form.method = 'POST';
                        form.style.display = 'none'; // Sembunyikan form

                        // Tambahkan CSRF token
                        let csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);

                        // Tambahkan method spoofing untuk DELETE
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
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Permohonan SITR']]" />

        <!-- Scrollable -->
        <div class="card">
            <div class="card-body d-block d-lg-flex border-bottom">
                <h5 class="text-start">Permohonan Ijin Pemanfaatan Tata Ruang</h5>
                @can('create permohonan')
                    <a href="{{ route('permohonan.create') }}" class="btn btn-primary ms-0 ms-lg-auto">Tambahkan Permohonan</a>
                @endcan
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-scrollableTable table table-stripped table-responsive">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Kabupaten</th>
                            <th>Rencana Usaha</th>
                            <th>Tanggal Permohonan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!--/ Scrollable -->
    </div>
@endsection
