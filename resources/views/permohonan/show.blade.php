@extends('layouts.app')
@section('title', 'Detail Permohonan ' . strtoupper($permohonan->var_type))

@section('page-script')
    {{-- HAPUS INI: <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.js"></script> --}}
    {{-- Kita nggak butuh library tambahan buat PDF --}}
@endsection

@section('content')
    {{-- ... (kode view 'show.blade.php' kamu) ... --}}
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Permohonan ' . strtoupper($permohonan->var_type), 'url' => route('permohonan.index') . '?type=' . $permohonan->var_type], ['text' => 'Detail']]" />

        <div class="card">
            <div class="card-header border-bottom mb-3">
                <h5 class="mb-0">Detail Permohonan {{ strtoupper($permohonan->var_type) }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12 col-lg-8">
                        <h5 class="fw-bold"># Informasi Pengusul</h5>
                        <dl class="row mb-0">
                            @if ($permohonan->var_nik)
                                <dt class="col-sm-5">NIK</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_nik }}</dd>
                            @endif

                            @if ($permohonan->var_nama)
                                <dt class="col-sm-5">Nama</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_nama }}</dd>
                            @endif

                            @if ($permohonan->text_alamat)
                                <dt class="col-sm-5">Alamat</dt>
                                <dd class="col-sm-7">: {{ $permohonan->text_alamat }}</dd>
                            @endif

                            @if ($permohonan->nama_provinsi)
                                <dt class="col-sm-5">Provinsi</dt>
                                <dd class="col-sm-7">: {{ $permohonan->nama_provinsi }}</dd>
                            @endif

                            @if ($permohonan->nama_kabupaten)
                                <dt class="col-sm-5">Kabupaten</dt>
                                <dd class="col-sm-7">: {{ $permohonan->nama_kabupaten }}</dd>
                            @endif

                            @if ($permohonan->nama_kecamatan)
                                <dt class="col-sm-5">Kecamatan</dt>
                                <dd class="col-sm-7">: {{ $permohonan->nama_kecamatan }}</dd>
                            @endif

                            @if ($permohonan->nama_kelurahan)
                                <dt class="col-sm-5">Kelurahan</dt>
                                <dd class="col-sm-7">: {{ $permohonan->nama_kelurahan }}</dd>
                            @endif

                            @if ($permohonan->var_email)
                                <dt class="col-sm-5">Email</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_email }}</dd>
                            @endif

                            @if ($permohonan->var_no_telp)
                                <dt class="col-sm-5">No. Telp</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_no_telp }}</dd>
                            @endif

                            @if ($permohonan->var_no_ponsel)
                                <dt class="col-sm-5">No. Ponsel</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_no_ponsel }}</dd>
                            @endif
                        </dl>
                    </div>
                    <div class="col-12 col-lg-8">
                        <h5 class="fw-bold mt-4"># Data Usaha</h5>
                        <dl class="row mb-0">
                            @if ($permohonan->var_nama_usaha)
                                <dt class="col-sm-5">Nama Usaha</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_nama_usaha }}</dd>
                            @endif

                            @if ($permohonan->var_bentuk_usaha)
                                <dt class="col-sm-5">Bentuk Usaha</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_bentuk_usaha }}</dd>
                            @endif

                            @if ($permohonan->text_alamat_usaha)
                                <dt class="col-sm-5">Alamat Usaha</dt>
                                <dd class="col-sm-7">: {{ $permohonan->text_alamat_usaha }}</dd>
                            @endif

                            @if ($permohonan->nama_kecamatan_usaha)
                                <dt class="col-sm-5">Kecamatan Usaha</dt>
                                <dd class="col-sm-7">: {{ $permohonan->nama_kecamatan_usaha }}</dd>
                            @endif

                            @if ($permohonan->nama_kelurahan_usaha)
                                <dt class="col-sm-5">Kelurahan Usaha</dt>
                                <dd class="col-sm-7">: {{ $permohonan->nama_kelurahan_usaha }}</dd>
                            @endif

                            @if ($permohonan->var_rencana_usaha)
                                <dt class="col-sm-5">Rencana Usaha</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_rencana_usaha }}</dd>
                            @endif

                            @if ($permohonan->dec_rencana_luas_lantai)
                                <dt class="col-sm-5">Rencana Luas Lantai</dt>
                                <dd class="col-sm-7">
                                    : {{ number_format($permohonan->dec_rencana_luas_lantai, 2) . ' m²' }}
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="row mb-4">
                    @if ($permohonan->json_geometry)
                        <div class="col-12">
                            <h5 class="fw-bold"># Geometri Lokasi</h5>
                            <a href="{{ route('permohonan.downloadKml', $permohonan->id) }}"
                                class="btn btn-sm btn-info mb-3">
                                <i class="ti ti-download me-1"></i> Download File KML
                            </a>
                            <div id="map" style="height: 500px; width: 100%;" class="mb-2 rounded"></div>
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

                        </div>
                    @endif
                    <div class="col-md-6 mt-3">
                        <h5 class="fw-bold"># Administrasi</h5>
                        <dl class="row mb-0">
                            @if ($permohonan->var_nomor_permohonan)
                                <dt class="col-sm-5">Nomor Permohonan</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_nomor_permohonan }}</dd>
                            @endif

                            @if ($permohonan->date_tanggal_permohonan)
                                <dt class="col-sm-5">Tanggal Permohonan</dt>
                                <dd class="col-sm-7">:
                                    {{ \Carbon\Carbon::parse($permohonan->date_tanggal_permohonan)->format('d-m-Y') }}
                                </dd>
                            @endif

                            @if ($permohonan->var_nomor_pengesahan)
                                <dt class="col-sm-5">Nomor Pengesahan</dt>
                                <dd class="col-sm-7">: {{ $permohonan->var_nomor_pengesahan }}</dd>
                            @endif

                            @if ($permohonan->date_tanggal_pengesahan)
                                <dt class="col-sm-5">Tanggal Pengesahan</dt>
                                <dd class="col-sm-7">:
                                    {{ \Carbon\Carbon::parse($permohonan->date_tanggal_pengesahan)->format('d-m-Y') }}
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>
                @if ($permohonan->var_type == 'kkpr')
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="fw-bold mt-4"># Lampiran Permohonan KKPR</h5>
                            <dl class="row mb-0">
                                @if ($permohonan->var_fotocopy_ktp_attachment)
                                    <dt class="col-sm-5">Fotocopy KTP</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_fotocopy_ktp_attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_fotocopy_npwp_attachment)
                                    <dt class="col-sm-5">Fotocopy NPWP</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_fotocopy_npwp_attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_foto_lokasi_rencana_kegiatan_attachment)
                                    <dt class="col-sm-5">Foto Lokasi Rencana Kegiatan</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_foto_lokasi_rencana_kegiatan_attachment }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_titik_koordinat_attachment)
                                    <dt class="col-sm-5">Titik Koordinat</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_titik_koordinat_attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_sitr_attachment)
                                    <dt class="col-sm-5">SITR</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_sitr_attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_lp2b_attachment)
                                    <dt class="col-sm-5">LP2B</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_lp2b_attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_bukti_penguasaan_tanah_attachment)
                                    <dt class="col-sm-5">Bukti Penguasaan Tanah</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_bukti_penguasaan_tanah_attachment }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_rencana_teknis_bangunan_attachment)
                                    <dt class="col-sm-5">Rencana Teknis Bangunan</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_rencana_teknis_bangunan_attachment }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_ptp_kkpr_nonberusaha_attachment)
                                    <dt class="col-sm-5">PTP KKPR Nonberusaha</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_ptp_kkpr_nonberusaha_attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                                @if ($permohonan->var_akta_pendirian_badan_attachment)
                                    <dt class="col-sm-5">Akta Pendirian Badan</dt>
                                    <dd class="col-sm-7">
                                        <a href="{{ $permohonan->var_akta_pendirian_badan_attachment }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                        </a>
                                    </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mt-4"># Dokumen Terkait</h5>
                            <form action="{{ route('permohonan.generateDocuments', $permohonan->id) }}" method="POST">
                                @csrf
                                @if ($permohonan->permohonanTemplateDocs->count() > 0)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-file me-1"></i> Generate Dokumen
                                    </button>
                                @endif
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
                                    @forelse ($permohonan->permohonanTemplateDocs as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $doc->var_nama }}</strong></td>
                                            <td>
                                                @if ($doc->var_generated_file_path)
                                                    <span class="badge bg-label-success">Sudah Dibuat</span>
                                                @else
                                                    <span class="badge bg-label-warning">Belum Dibuat</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($doc->var_generated_file_path)
                                                    <div class="d-flex">
                                                        <a href="{{ $doc->var_generated_file_path }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                            <i class="fa-solid fa-eye me-1"></i> Lihat
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary preview-btn"
                                                            data-bs-toggle="modal" data-bs-target="#previewModal"
                                                            data-file-url="{{ $doc->var_generated_file_path }}"
                                                            data-file-name="{{ $doc->var_nama }}">
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

                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold mt-4"># Timeline Histori</h5>
                        <ul class="list-group">
                            @forelse ($permohonan->activities->sortByDesc('created_at') as $activity)
                                <li class="list-group-item border-0 border-bottom rounded-0 py-3">
                                    <div class="d-flex justify-content-between">

                                        <small class="text-muted text-nowrap me-5">
                                            {{ $activity->created_at->format('d M Y, H:i') }}
                                        </small>
                                        <div class="w-100">
                                            <p class="mb-1 fw-bold">
                                                {{ $activity->description }}
                                            </p>
                                            @if ($activity->event === 'updated' && $activity->properties->has('old'))
                                                <div class="bg-light p-2 rounded small mt-2">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($activity->properties['attributes'] as $key => $newValue)
                                                            {{-- Skip timestamp dan field yg gak penting --}}
                                                            @continue(in_array($key, ['updated_at', 'request_tte_date', 'approved_date']))

                                                            @if (isset($activity->properties['old'][$key]))
                                                                <li class="mb-1">
                                                                    <i class="ti ti-edit text-warning me-1"></i>
                                                                    Ubah <strong>{{ ucwords(str_replace(['var_', 'enum_', 'text_'], '', str_replace('_', ' ', $key))) }}</strong>:
                                                                    <br>
                                                                    <span class="text-decoration-line-through text-muted ms-4">{{ $activity->properties['old'][$key] ?: '-' }}</span>
                                                                    <i class="ti ti-arrow-right mx-1"></i>
                                                                    <span class="text-success fw-bold">{{ $newValue ?: '-' }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            @if ($activity->getExtraProperty('catatan_penolakan'))
                                                <div class="alert alert-warning p-2 mt-2 mb-0 small">
                                                    <strong>Catatan:</strong> {{ $activity->getExtraProperty('catatan_penolakan') }}
                                                </div>
                                            @endif

                                            <small class="text-muted d-block mt-1">
                                                Oleh: {{ $activity->causer ? $activity->causer->name : 'Sistem' }}
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted fst-italic">
                                    Belum ada histori aktivitas.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <div id="modal-preview-content" class="h-100 w-100">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const previewModal = document.getElementById('previewModal');
            const modalTitle = document.getElementById('previewModalLabel');
            const previewContainer = document.getElementById('modal-preview-content');

            previewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const fileUrl = button.getAttribute('data-file-url');
                const fileName = button.getAttribute('data-file-name');

                modalTitle.textContent = 'Preview: ' + (fileName || 'Dokumen');

                previewContainer.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;

                setTimeout(() => {
                     previewContainer.innerHTML = `
                        <iframe src="${fileUrl}" width="100%" height="100%" style="border: none;" allowfullscreen>
                            <p>Browser kamu tidak mendukung preview PDF.
                            <a href="${fileUrl}" target="_blank">Download file</a> sebagai gantinya.</p>
                        </iframe>
                    `;
                }, 300);
            });

            previewModal.addEventListener('hidden.bs.modal', function () {
                previewContainer.innerHTML = '';
            });
        });
    </script>
@endsection
