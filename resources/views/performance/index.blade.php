@extends('layouts.app')
@section('title', 'Dashboard Performa')

@section('page-script')
    <script src="https://cdn.datatables.net/2.1.8/js/jquery.dataTables.min.js"></script>

    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(e) {

            // =======================================================
            // 1. Inisialisasi Chart Performa Server
            // =======================================================
            const globalChartEl = document.querySelector('#globalChart');
            if (globalChartEl) {
                const globalChartOptions = {
                    chart: {
                        height: 350,
                        type: "radialBar"
                    },
                    colors: ['#696cff', '#ff3e1d', '#03c3ec'], // Primary, Danger, Info
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: "40%"
                            },
                            track: {
                                margin: 10,
                                background: "#f0f2f8"
                            },
                            dataLabels: {
                                name: {
                                    fontSize: "1.2rem",
                                },
                                value: {
                                    fontSize: "1rem",
                                    color: "#566a7f",
                                },
                                // Kita hapus 'total' karena legend sudah cukup jelas
                            }
                        }
                    },
                    grid: {
                        padding: {
                            top: -20,
                            bottom: -20
                        }
                    },
                    legend: {
                        show: true,
                        position: "bottom",
                    },
                    stroke: {
                        lineCap: "round"
                    },
                    series: [
                        {{ $globalData['cpuUsage'] ?? 0 }},
                        {{ $globalData['diskUsage'] ?? 0 }},
                        {{ $globalData['memoryUsage'] ?? 0 }}
                    ],
                    labels: ["CPU", "Disk", "Memory"]
                };
                const globalChart = new ApexCharts(globalChartEl, globalChartOptions);
                globalChart.render();
            }


            // =======================================================
            // 2. Inisialisasi Tabel untuk Log Query
            // =======================================================
            $('#dataTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json"
                },
                "pageLength": 5, // Tampilkan 5 query per halaman
                "lengthMenu": [5, 10, 25]
            });

        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Dashboard Performa</h4>

        <!-- 1. Performa Server (Global) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bx bx-server me-2"></i>Performa Server (Global)</h5>
            </div>
            <div class="card-body">
                <div class="row gy-4">
                    <!-- Grafik Radial -->
                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                        <div id="globalChart"></div>
                    </div>
                    <!-- Info Detail Server -->
                    <div class="col-md-6">
                        <div class="list-group">
                            <a href="javascript:void(0);"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-microchip me-2"></i> CPU</span>
                                <small
                                    class="text-muted">{{ $globalData['systemInfo']['cpu'] ?? 'Tidak diketahui' }}</small>
                            </a>
                            <a href="javascript:void(0);"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-memory me-2"></i> RAM</span>
                                <small
                                    class="text-muted">{{ $globalData['systemInfo']['memory'] ?? 'Tidak diketahui' }}</small>
                            </a>
                            <a href="javascript:void(0);"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-user-clock me-2"></i> Sesi Aktif</span>
                                <span class="badge bg-label-success">{{ $appData['activeUsers'] ?? 0 }}</span>
                            </a>
                            <a href="javascript:void(0);"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-tasks me-2"></i> Antrian Jobs</span>
                                <span class="badge bg-label-warning">{{ $appData['jobsPending'] ?? 0 }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Performa Aplikasi (Request Saat Ini) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bx bx-rocket me-2"></i>Performa Aplikasi (Request Saat Ini)</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="fas fa-stopwatch"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Waktu Eksekusi</h6>
                                    <small class="text-muted">Seberapa cepat halaman ini dimuat</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $appData['executionTime'] ?? 0 }} ms</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="fas fa-memory"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Penggunaan Memori</h6>
                                    <small class="text-muted">RAM yang dipakai oleh request ini</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $appData['memoryUsage'] ?? 0 }} MB</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="fas fa-database"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Total Query</h6>
                                    <small class="text-muted">Jumlah query ke database</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $appData['queryCount'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- 3. Detail Query Database -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bx bx-data me-2"></i>Log Query Database</h5>
            </div>
            <div class="card-body">
                <div class="card-datatable table-responsive text-start text-nowrap">
                    <table class="table table-bordered table-responsive-sm table-responsive-md table-responsive-xl w-100"
                        id="dataTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Query</th>
                                <th>Waktu (ms)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appData['queries'] as $query)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <code
                                            style="white-space: pre-wrap; word-break: break-all;">{{ $query['query'] }}</code>
                                    </td>
                                    <td>{{ $query['time'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada query yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
