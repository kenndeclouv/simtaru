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
                            var deleteUrl = '/permohonan/' + row.id;
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');
                            return `
                            <a href="${showUrl}" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Permohonan">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="${editUrl}" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Permohonan">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            
                            <form action="${deleteUrl}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')" >
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hapus Permohonan">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
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
                <a href="{{ route('permohonan.create') }}" class="btn btn-primary ms-0 ms-lg-auto">Tambahkan Permohonan</a>
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
