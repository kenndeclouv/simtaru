@extends('layouts.app')
@section('title', 'Detail Permohonan SITR')

@section('page-script')

    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.js"></script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Permohonan SITR', 'url' => route('permohonan.index')], ['text' => 'Detail']]" />

        <div class="card">
            <div class="card-header border-bottom mb-3">
                <h5 class="mb-0">Detail Permohonan</h5>
                
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12 col-lg-8">
                        <h5 class="fw-bold"># Informasi Pengusul</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">NIK</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_nik }}</dd>

                            <dt class="col-sm-5">Nama</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_nama }}</dd>

                            <dt class="col-sm-5">Alamat</dt>
                            <dd class="col-sm-7">: {{ $permohonan->text_alamat }}</dd>

                            <dt class="col-sm-5">Provinsi</dt>
                            <dd class="col-sm-7">: {{ $permohonan->nama_provinsi }}</dd>

                            <dt class="col-sm-5">Kabupaten</dt>
                            <dd class="col-sm-7">: {{ $permohonan->nama_kabupaten }}</dd>

                            <dt class="col-sm-5">Kecamatan</dt>
                            <dd class="col-sm-7">: {{ $permohonan->nama_kecamatan }}</dd>

                            <dt class="col-sm-5">Kelurahan</dt>
                            <dd class="col-sm-7">: {{ $permohonan->nama_kelurahan }}</dd>

                            <dt class="col-sm-5">Email</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_email }}</dd>

                            <dt class="col-sm-5">No. Telp</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_no_telp ?? '-' }}</dd>

                            <dt class="col-sm-5">No. Ponsel</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_no_ponsel ?? '-' }}</dd>
                        </dl>
                    </div>
                    <div class="col-12 col-lg-8">
                        <h5 class="fw-bold mt-4"># Data Usaha</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Nama Usaha</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_nama_usaha }}</dd>

                            <dt class="col-sm-5">Bentuk Usaha</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_bentuk_usaha }}</dd>

                            <dt class="col-sm-5">Alamat Usaha</dt>
                            <dd class="col-sm-7">: {{ $permohonan->text_alamat_usaha }}</dd>

                            <dt class="col-sm-5">Kecamatan Usaha</dt>
                            <dd class="col-sm-7">:
                                {{ $permohonan->nama_kecamatan_usaha }}</dd>

                            <dt class="col-sm-5">Kelurahan Usaha</dt>
                            <dd class="col-sm-7">:
                                {{ $permohonan->nama_kelurahan_usaha }}</dd>

                            <dt class="col-sm-5">Rencana Usaha</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_rencana_usaha }}</dd>

                            <dt class="col-sm-5">Rencana Luas Lantai</dt>
                            <dd class="col-sm-7">:
                                {{ $permohonan->dec_rencana_luas_lantai ? number_format($permohonan->dec_rencana_luas_lantai, 2) . ' m²' : '-' }}
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold"># Geometri Lokasi</h5>
                        @if ($permohonan->json_geometry)
                            <div id="map" style="height: 500px; width: 100%;" class="mb-2 rounded"></div>
                            {{-- <dl class="row mb-0">
                                    <dt class="col-sm-5">GeoJSON</dt>
                                    <dd class="col-sm-7">
                                        <pre class="bg-light p-2 rounded small">{{ Str::limit($permohonan->json_geometry, 300) }}</pre>
                                    </dd>
                                </dl> --}}
                            <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_api_key') }}&libraries=drawing">
                            </script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var geojson = {!! json_encode($permohonan->json_geometry) !!};
                                    if (typeof geojson === 'string') {
                                        try {
                                            geojson = JSON.parse(geojson);
                                        } catch (e) {
                                            geojson = null;
                                        }
                                    }
                                    if (!geojson) return;

                                    function getCenter(geojson) {
                                        if (geojson.type === 'Point') {
                                            return {
                                                lat: geojson.coordinates[1],
                                                lng: geojson.coordinates[0]
                                            };
                                        }
                                        if (geojson.type === 'Polygon') {
                                            var coords = geojson.coordinates[0];
                                            var lats = coords.map(c => c[1]);
                                            var lngs = coords.map(c => c[0]);
                                            var lat = (Math.min(...lats) + Math.max(...lats)) / 2;
                                            var lng = (Math.min(...lngs) + Math.max(...lngs)) / 2;
                                            return {
                                                lat,
                                                lng
                                            };
                                        }
                                        if (geojson.type === 'LineString') {
                                            var coords = geojson.coordinates;
                                            var lats = coords.map(c => c[1]);
                                            var lngs = coords.map(c => c[0]);
                                            var lat = (Math.min(...lats) + Math.max(...lats)) / 2;
                                            var lng = (Math.min(...lngs) + Math.max(...lngs)) / 2;
                                            return {
                                                lat,
                                                lng
                                            };
                                        }
                                        return {
                                            lat: -7.797068,
                                            lng: 110.370529
                                        };
                                    }

                                    var center = getCenter(geojson);

                                    var map = new google.maps.Map(document.getElementById('map'), {
                                        center: center,
                                        zoom: 16,
                                    });

                                    map.data.addGeoJson(geojson);

                                    map.data.setStyle({
                                        fillColor: '#1976d2',
                                        strokeColor: '#1976d2',
                                        strokeWeight: 2,
                                        fillOpacity: 0.2
                                    });

                                    var bounds = new google.maps.LatLngBounds();
                                    map.data.forEach(function(feature) {
                                        feature.getGeometry().forEachLatLng(function(latlng) {
                                            bounds.extend(latlng);
                                        });
                                    });
                                    if (!bounds.isEmpty()) {
                                        map.fitBounds(bounds);
                                    }
                                });
                            </script>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-md-6 mt-3">
                        <h5 class="fw-bold"># Administrasi</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Nomor Permohonan</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_nomor_permohonan }}</dd>

                            <dt class="col-sm-5">Tanggal Permohonan</dt>
                            <dd class="col-sm-7">:
                                {{ \Carbon\Carbon::parse($permohonan->date_tanggal_permohonan)->format('d-m-Y') }}</dd>

                            <dt class="col-sm-5">Nomor Pengesahan</dt>
                            <dd class="col-sm-7">: {{ $permohonan->var_nomor_pengesahan ?? '-' }}</dd>

                            <dt class="col-sm-5">Tanggal Pengesahan</dt>
                            <dd class="col-sm-7">:
                                {{ $permohonan->date_tanggal_pengesahan ? \Carbon\Carbon::parse($permohonan->date_tanggal_pengesahan)->format('d-m-Y') : '-' }}
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        {{-- <h5 class="fw-bold"># Pilihan Redaksi</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-2">Redaksi</dt>
                            <dd class="col-sm-10">
                                @forelse ($permohonan->templateDocs as $doc)
                                    <li>
                                        {{ $doc->var_nama }} -
                                        @if ($doc->pivot->var_generated_file_path)
                                            (<a href="{{ asset('storage/' . $doc->pivot->var_generated_file_path) }}"
                                                target="_blank">Download</a>
                                            <a href="#" class="text-secondary preview-btn" data-bs-toggle="modal"
                                                data-bs-target="#previewModal"
                                                data-file-url="{{ asset('storage/' . $doc->pivot->var_generated_file_path) }}">
                                                Preview
                                            </a>)
                                        @else
                                            <span class="text-muted">Belum di-generate</span>
                                        @endif
                                    </li>
                                @empty
                                    <li>Tidak ada template dokumen yang terhubung.</li>
                                @endforelse
                            </dd>
                        </dl> --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mt-4"># Dokumen Terkait</h5>
                            <form action="{{ route('permohonan.generateDocuments', $permohonan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-file me-1"></i> Generate Dokumen
                                </button>
                            </form>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Dokumen</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($permohonan->templateDocs as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $doc->var_nama }}</strong></td>
                                            <td>
                                                @if ($doc->pivot->var_generated_file_path)
                                                    <span class="badge bg-label-success">Sudah Dibuat</span>
                                                @else
                                                    <span class="badge bg-label-warning">Belum Dibuat</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($doc->pivot->var_generated_file_path)
                                                    <div class="d-flex">
                                                        <a href="{{ asset('storage/' . $doc->pivot->var_generated_file_path) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                            <i class="bx bx-download me-1"></i> Download
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary preview-btn"
                                                            data-bs-toggle="modal" data-bs-target="#previewModal"
                                                            data-file-url="{{ asset('storage/' . $doc->pivot->var_generated_file_path) }}">
                                                            <i class="bx bx-show me-1"></i> Preview
                                                        </button>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada template dokumen yang
                                                terhubung.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header mb-4">
                    <h5 class="modal-title" id="previewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Konten preview akan dimuat di sini oleh JavaScript --}}
                    <div id="modal-preview-content" class="overflow-hidden rounded" style="min-height: 400px;"></div>
                </div>
                <div class="modal-footer mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const previewModal = document.getElementById('previewModal');

            previewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const fileUrl = button.getAttribute('data-file-url');

                const previewContainer = document.getElementById('modal-preview-content');

                previewContainer.innerHTML = '<p class="text-center"><em>Loading preview...</em></p>';

                fetch(fileUrl)
                    .then(response => response.blob())
                    .then(blob => {
                        docx.renderAsync(blob, previewContainer)
                            .then(x => console.log("Preview berhasil dirender di modal."));
                    })
                    .catch(error => {
                        console.error('Gagal memuat preview di modal:', error);
                        previewContainer.innerHTML =
                            '<p class="text-center text-danger">Gagal memuat file preview.</p>';
                    });
            });
        });
    </script>
@endsection
