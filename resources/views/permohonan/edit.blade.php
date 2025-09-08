@extends('layouts.app')

@section('title', 'Edit Permohonan SITR')

@section('page-script')
    <script>
        $('.select2').select2();

        const PROVINSI_ID_DEFAULT = '35';
        const KABUPATEN_ID_DEFAULT = '3508';

        function reloadSelect2($select, options, placeholder = '') {
            $select.html(options);
            $select.select2({
                width: '100%',
                placeholder: placeholder,
                dropdownParent: $select.parent()
            });
        }
        async function populateSelect($select, url, placeholder, selectedValue = null) {
            $select.html('<option value="">Memuat...</option>').prop('disabled', true);
            try {
                const data = await $.getJSON(url);
                let options = `<option value="">${placeholder}</option>`;
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.nama}</option>`;
                });

                reloadSelect2($select, options, placeholder);

                if (selectedValue) {
                    setTimeout(function() {
                        $select.val(selectedValue).trigger('change.select2');
                    }, 50);
                }

                $select.prop('disabled', false);
            } catch (error) {
                console.error(`Gagal memuat data dari ${url}`, error);
                $select.html(`<option value="">Gagal memuat</option>`).prop('disabled', true);
                throw error;
            }
        };

        async function initializeLocationDropdowns($group, values) {
            const {
                prov,
                kab,
                kec,
                kel
            } = values;
            const $prov = $group.find('.provinsi');
            const $kab = $group.find('.kabupaten');
            const $kec = $group.find('.kecamatan');
            const $kel = $group.find('.kelurahan');

            await populateSelect($prov, '/data-indonesia/provinsi.json', '-- Pilih Provinsi --', prov);

            if (prov) {
                await populateSelect($kab, `/data-indonesia/kabupaten/${prov}.json`, '-- Pilih Kabupaten --', kab);
            }

            if (kab) {
                await populateSelect($kec, `/data-indonesia/kecamatan/${kab}.json`, '-- Pilih Kecamatan --', kec);
            }

            if (kec) {
                await populateSelect($kel, `/data-indonesia/kelurahan/${kec}.json`, '-- Pilih Kelurahan --', kel);
            }
        }

        async function initializeUsahaLocationDropdowns($group, values) {
            const {
                kec,
                kel
            } = values;
            const $prov = $group.find('.provinsi');
            const $kab = $group.find('.kabupaten');
            const $kec = $group.find('.kecamatan');
            const $kel = $group.find('.kelurahan');

            $prov.val(PROVINSI_ID_DEFAULT);
            $kab.val(KABUPATEN_ID_DEFAULT);

            await populateSelect($kec, `/data-indonesia/kecamatan/${KABUPATEN_ID_DEFAULT}.json`,
                '-- Pilih Kecamatan --', kec);

            if (kec) {
                await populateSelect($kel, `/data-indonesia/kelurahan/${kec}.json`, '-- Pilih Kelurahan --', kel);
            }
        }

        function bindLocationChangeEvents() {
            $('.location-group').each(function() {
                const $group = $(this);
                const $prov = $group.find('.provinsi');
                const $kab = $group.find('.kabupaten');
                const $kec = $group.find('.kecamatan');
                const $kel = $group.find('.kelurahan');

                $prov.on('change', function() {
                    const provId = $(this).val();
                    $kab.val(null).trigger('change');
                    populateSelect($kab, `/data-indonesia/kabupaten/${provId}.json`,
                        '-- Pilih Kabupaten --');
                });

                $kab.on('change', function() {
                    const kabId = $(this).val();
                    $kec.val(null).trigger('change');
                    populateSelect($kec, `/data-indonesia/kecamatan/${kabId}.json`,
                        '-- Pilih Kecamatan --');
                });

                $kec.on('change', function() {
                    const kecId = $(this).val();
                    $kel.val(null).trigger('change');
                    populateSelect($kel, `/data-indonesia/kelurahan/${kecId}.json`,
                        '-- Pilih Kelurahan --');
                });
            });
        }


        $(document).ready(async function() {
            $('form').css('opacity', 0.5);

            const pengusulValues = {
                prov: @json(old('var_provinsi', $permohonan->var_provinsi ?? '')),
                kab: @json(old('var_kabupaten', $permohonan->var_kabupaten ?? '')),
                kec: @json(old('var_kecamatan', $permohonan->var_kecamatan ?? '')),
                kel: @json(old('var_kelurahan', $permohonan->var_kelurahan ?? ''))
            };
            const usahaValues = {
                kec: @json(old('var_kecamatan_usaha', $permohonan->var_kecamatan_usaha ?? '')),
                kel: @json(old('var_kelurahan_usaha', $permohonan->var_kelurahan_usaha ?? ''))
            };

            await initializeLocationDropdowns($('.location-group').eq(0), pengusulValues);
            await initializeUsahaLocationDropdowns($('.location-group').eq(1), usahaValues);

            bindLocationChangeEvents();

            $('form').css('opacity', 1);
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_api_key') }}&libraries=drawing">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let geojson = {!! json_encode($permohonan->json_geometry) !!};
            if (typeof geojson === 'string') {
                try {
                    geojson = JSON.parse(geojson);
                } catch (e) {
                    geojson = null;
                }
            }

            let center = {
                lat: -8.129955,
                lng: 113.223066
            };
            if (geojson) {
                if (geojson.geometry && geojson.geometry.type === 'Point') {
                    center = {
                        lat: geojson.geometry.coordinates[1],
                        lng: geojson.geometry.coordinates[0]
                    };
                } else if (geojson.geometry && (geojson.geometry.type === 'Polygon' || geojson.geometry.type ===
                        'LineString')) {
                    const firstCoord = geojson.geometry.coordinates[0][0];
                    center = {
                        lat: firstCoord[1],
                        lng: firstCoord[0]
                    };
                }
            }

            const map = new google.maps.Map(document.getElementById("map"), {
                center: center,
                zoom: 11,
                mapTypeId: "hybrid",
            });

            const drawingManager = new google.maps.drawing.DrawingManager({
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_LEFT,
                    drawingModes: ["marker", "polyline", "polygon"],
                },
                polygonOptions: {
                    editable: true,
                    draggable: true,
                    fillColor: "#DAD155",
                    fillOpacity: 0.5,
                    strokeWeight: 2
                },
                polylineOptions: {
                    editable: true,
                    draggable: true,
                    strokeColor: "#DAD155",
                    strokeOpacity: 1.0,
                    strokeWeight: 3
                },
                markerOptions: {
                    draggable: true
                },
            });
            drawingManager.setMap(map);
            let currentShape = null;

            if (geojson && geojson.geometry) {
                const type = geojson.geometry.type;
                const coords = geojson.geometry.coordinates;

                if (type === 'Point') {
                    currentShape = new google.maps.Marker({
                        position: {
                            lat: coords[1],
                            lng: coords[0]
                        },
                        map: map,
                        draggable: true,
                    });
                } else if (type === 'Polygon') {
                    const paths = coords[0].map(c => ({
                        lat: c[1],
                        lng: c[0]
                    }));
                    currentShape = new google.maps.Polygon({
                        paths: paths,
                        ...drawingManager.polygonOptions,
                        map: map,
                    });
                } else if (type === 'LineString') {
                    const path = coords.map(c => ({
                        lat: c[1],
                        lng: c[0]
                    }));
                    currentShape = new google.maps.Polyline({
                        path: path,
                        ...drawingManager.polylineOptions,
                        map: map,
                    });
                }

                const bounds = new google.maps.LatLngBounds();
                if (currentShape.getPath) {
                    currentShape.getPath().forEach(p => bounds.extend(p));
                } else {
                    bounds.extend(currentShape.getPosition());
                }
                map.fitBounds(bounds);
                if (map.getZoom() > 18) map.setZoom(18);
            }

            function updateGeometry(shape) {
                const type = shape.getMap ? (shape.getPath ? (shape.getPaths ? 'polygon' : 'polyline') : 'marker') :
                    null;
                let newGeoJson = null;

                if (type === 'marker') {
                    const pos = shape.getPosition();
                    newGeoJson = {
                        type: "Feature",
                        geometry: {
                            type: "Point",
                            coordinates: [pos.lng(), pos.lat()]
                        },
                        properties: {}
                    };
                } else if (type === 'polygon') {
                    const path = shape.getPath().getArray();
                    const coordinates = path.map(p => [p.lng(), p.lat()]);
                    coordinates.push(coordinates[0]);
                    newGeoJson = {
                        type: "Feature",
                        geometry: {
                            type: "Polygon",
                            coordinates: [coordinates]
                        },
                        properties: {}
                    };
                } else if (type === 'polyline') {
                    const path = shape.getPath().getArray();
                    const coordinates = path.map(p => [p.lng(), p.lat()]);
                    newGeoJson = {
                        type: "Feature",
                        geometry: {
                            type: "LineString",
                            coordinates: coordinates
                        },
                        properties: {}
                    };
                }
                document.getElementById("json_geometry").value = JSON.stringify(newGeoJson);
            }

            google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
                if (currentShape) {
                    currentShape.setMap(null);
                }
                currentShape = event.overlay;
                drawingManager.setDrawingMode(null);
                updateGeometry(currentShape);
                if (currentShape.getPath) {
                    google.maps.event.addListener(currentShape.getPath(), 'set_at', () => updateGeometry(
                        currentShape));
                    google.maps.event.addListener(currentShape.getPath(), 'insert_at', () => updateGeometry(
                        currentShape));
                } else {
                    google.maps.event.addListener(currentShape, 'dragend', () => updateGeometry(
                        currentShape));
                }
            });

            if (currentShape) {
                if (currentShape.getPath) {
                    google.maps.event.addListener(currentShape.getPath(), 'set_at', () => updateGeometry(
                        currentShape));
                    google.maps.event.addListener(currentShape.getPath(), 'insert_at', () => updateGeometry(
                        currentShape));
                } else {
                    google.maps.event.addListener(currentShape, 'dragend', () => updateGeometry(currentShape));
                }
            }

        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Permohonan SITR', 'url' => route('permohonan.index')], ['text' => 'Edit Permohonan']]" />

        <form action="{{ route('permohonan.update', $permohonan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <h5 class="card-header border-bottom mb-3">Informasi Pengusul</h5>

                        <div class="card-body">

                            <div class="mb-4 row">
                                <label for="var_nik" class="col-sm-3 col-form-label">NIK</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_nik" id="var_nik"
                                        class="form-control  @error('var_nik') is-invalid @enderror"
                                        value="{{ old('var_nik', $permohonan->var_nik ?? '') }}" maxlength="16" required>
                                    @errorFeedback('var_nik')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_nama" class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_nama" id="var_nama"
                                        class="form-control  @error('var_nama') is-invalid @enderror"
                                        value="{{ old('var_nama', $permohonan->var_nama ?? '') }}" required>
                                    @errorFeedback('var_nama')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="text_alamat" class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9">
                                    <textarea name="text_alamat" id="text_alamat" class="form-control  @error('text_alamat') is-invalid @enderror"
                                        rows="3" required>{{ old('text_alamat', $permohonan->text_alamat ?? '') }}</textarea>
                                    @errorFeedback('text_alamat')
                                </div>
                            </div>

                            <div class="location-group">
                                <div class="mb-4 row">
                                    <label for="var_provinsi" class="col-sm-3 col-form-label">Provinsi</label>
                                    <div class="col-sm-9">
                                        <select name="var_provinsi" id="var_provinsi"
                                            class="form-select  select2 provinsi @error('var_provinsi') is-invalid @enderror">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                        @errorFeedback('var_provinsi')
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label for="var_kabupaten" class="col-sm-3 col-form-label">Kabupaten</label>
                                    <div class="col-sm-9">
                                        <select name="var_kabupaten" id="var_kabupaten"
                                            class="form-select select2 kabupaten @error('var_kabupaten') is-invalid @enderror"
                                            disabled>
                                            <option value="">-- Pilih Kabupaten --</option>
                                        </select>
                                        @errorFeedback('var_kabupaten')
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label for="var_kecamatan" class="col-sm-3 col-form-label">Kecamatan</label>
                                    <div class="col-sm-9">
                                        <select name="var_kecamatan" id="var_kecamatan"
                                            class="form-select select2 kecamatan @error('var_kecamatan') is-invalid @enderror"
                                            disabled>
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                        @errorFeedback('var_kecamatan')
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label for="var_kelurahan" class="col-sm-3 col-form-label">Kelurahan</label>
                                    <div class="col-sm-9">
                                        <select name="var_kelurahan" id="var_kelurahan"
                                            class="form-select select2 kelurahan @error('var_kelurahan') is-invalid @enderror"
                                            disabled>
                                            <option value="">-- Pilih Kelurahan --</option>
                                        </select>
                                        @errorFeedback('var_kelurahan')
                                    </div>
                                </div>
                            </div>


                            <div class="mb-4 row">
                                <label for="var_email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="var_email" id="var_email"
                                        class="form-control  @error('var_email') is-invalid @enderror"
                                        value="{{ old('var_email', $permohonan->var_email ?? '') }}">
                                    @errorFeedback('var_email')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_no_telp" class="col-sm-3 col-form-label">No. Telp</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_no_telp" id="var_no_telp"
                                        class="form-control  @error('var_no_telp') is-invalid @enderror phone-mask"
                                        value="{{ old('var_no_telp', $permohonan->var_no_telp ?? '') }}" required>
                                    @errorFeedback('var_no_telp')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_no_ponsel" class="col-sm-3 col-form-label">No. Ponsel</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_no_ponsel" id="var_no_ponsel"
                                        class="form-control  @error('var_no_ponsel') is-invalid @enderror phone-mask"
                                        value="{{ old('var_no_ponsel', $permohonan->var_no_ponsel ?? '') }}" required>
                                    @errorFeedback('var_no_ponsel')
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <h5 class="card-header border-bottom mb-3">Data Usaha</h5>
                        <div class="card-body">

                            <div class="mb-4 row">
                                <label for="var_nama_usaha" class="col-sm-3 col-form-label">Nama Usaha</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_nama_usaha" id="var_nama_usaha"
                                        class="form-control  @error('var_nama_usaha') is-invalid @enderror"
                                        value="{{ old('var_nama_usaha', $permohonan->var_nama_usaha ?? '') }}" required>
                                    @errorFeedback('var_nama_usaha')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_bentuk_usaha" class="col-sm-3 col-form-label">Bentuk Usaha</label>
                                <div class="col-sm-9">
                                    <select name="var_bentuk_usaha" id="var_bentuk_usaha"
                                        class="form-select select2 @error('var_bentuk_usaha') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Bentuk Usaha --</option>
                                        <option value="Perorangan"
                                            {{ old('var_bentuk_usaha', $permohonan->var_bentuk_usaha ?? '') == 'Perorangan' ? 'selected' : '' }}>
                                            Perorangan
                                        </option>
                                        <option value="CV/UD"
                                            {{ old('var_bentuk_usaha', $permohonan->var_bentuk_usaha ?? '') == 'CV/UD' ? 'selected' : '' }}>
                                            CV/UD</option>
                                        <option value="PT"
                                            {{ old('var_bentuk_usaha', $permohonan->var_bentuk_usaha ?? '') == 'PT' ? 'selected' : '' }}>
                                            PT
                                        </option>
                                        <option value="BUMDES/BUMDESMa"
                                            {{ old('var_bentuk_usaha', $permohonan->var_bentuk_usaha ?? '') == 'BUMDES/BUMDESMa' ? 'selected' : '' }}>
                                            BUMDES/BUMDESMa</option>
                                        <option value="Yayasan"
                                            {{ old('var_bentuk_usaha', $permohonan->var_bentuk_usaha ?? '') == 'Yayasan' ? 'selected' : '' }}>
                                            Yayasan</option>
                                        <option value="Lainnya/Instansi"
                                            {{ old('var_bentuk_usaha', $permohonan->var_bentuk_usaha ?? '') == 'Lainnya/Instansi' ? 'selected' : '' }}>
                                            Lainnya/Instansi</option>
                                    </select>
                                    @errorFeedback('var_bentuk_usaha')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="text_alamat_usaha" class="col-sm-3 col-form-label">Alamat Usaha</label>
                                <div class="col-sm-9">
                                    <textarea name="text_alamat_usaha" id="text_alamat_usaha"
                                        class="form-control  @error('text_alamat_usaha') is-invalid @enderror" rows="3" required>{{ old('text_alamat_usaha', $permohonan->text_alamat_usaha ?? '') }}</textarea>
                                    @errorFeedback('text_alamat_usaha')
                                </div>
                            </div>

                            <div class="location-group">
                                <div class="mb-4 row d-none">
                                    <label for="var_provinsi_usaha" class="col-sm-3 col-form-label">Provinsi</label>
                                    <div class="col-sm-9">
                                        <select name="var_provinsi_usaha" id="var_provinsi_usaha"
                                            class="form-select select2 provinsi @error('var_provinsi_usaha') is-invalid @enderror">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                        @errorFeedback('var_provinsi_usaha')
                                    </div>
                                </div>

                                <div class="mb-4 row d-none">
                                    <label for="var_kabupaten_usaha" class="col-sm-3 col-form-label">Kabupaten</label>
                                    <div class="col-sm-9">
                                        <select name="var_kabupaten_usaha" id="var_kabupaten_usaha"
                                            class="form-select select2 kabupaten @error('var_kabupaten_usaha') is-invalid @enderror"
                                            disabled>
                                            <option value="">-- Pilih Kabupaten --</option>
                                        </select>
                                        @errorFeedback('var_kabupaten_usaha')
                                    </div>
                                </div>
                                <div class="mb-4 row">
                                    <label for="var_kecamatan_usaha" class="col-sm-3 col-form-label">Kecamatan</label>
                                    <div class="col-sm-9">
                                        <select name="var_kecamatan_usaha" id="var_kecamatan_usaha"
                                            class="form-select select2 kecamatan @error('var_kecamatan_usaha') is-invalid @enderror"
                                            disabled>
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                        @errorFeedback('var_kecamatan_usaha')
                                    </div>
                                </div>
                                <div class="mb-4 row">
                                    <label for="var_kelurahan_usaha" class="col-sm-3 col-form-label">Kelurahan</label>
                                    <div class="col-sm-9">
                                        <select name="var_kelurahan_usaha" id="var_kelurahan_usaha"
                                            class="form-select select2 kelurahan @error('var_kelurahan_usaha') is-invalid @enderror"
                                            disabled>
                                            <option value="">-- Pilih Kelurahan --</option>
                                        </select>
                                        @errorFeedback('var_kelurahan_usaha')
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_rencana_usaha" class="col-sm-3 col-form-label">Rencana Usaha</label>
                                <div class="col-sm-9">
                                    <textarea name="var_rencana_usaha" id="var_rencana_usaha"
                                        class="form-control  @error('var_rencana_usaha') is-invalid @enderror" required>{{ old('var_rencana_usaha', $permohonan->var_rencana_usaha ?? '') }}</textarea>
                                    @errorFeedback('var_rencana_usaha')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="dec_rencana_luas_lantai" class="col-sm-3 col-form-label">Rencana Luas
                                    Lantai</label>
                                <div class="col-sm-9">
                                    <input type="number" name="dec_rencana_luas_lantai" id="dec_rencana_luas_lantai"
                                        class="form-control  @error('dec_rencana_luas_lantai') is-invalid @enderror"
                                        value="{{ old('dec_rencana_luas_lantai', $permohonan->dec_rencana_luas_lantai ?? '') }}"
                                        required>
                                    @errorFeedback('dec_rencana_luas_lantai')
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-12 mt-4">
                    <div class="card">
                        <h5 class="card-header border-bottom mb-3">Peta Lokasi Usulan</h5>
                        <div class="card-body">
                            <div id="map" style="height: 500px; width: 100%;" class="mb-2 rounded"></div>
                            <input type="hidden" name="json_geometry" id="json_geometry"
                                value="{{ old('json_geometry', $permohonan->json_geometry ?? '') }}">
                            <div class="my-4 row">
                                <label for="nomor_permohonan" class="col-sm-3 col-form-label">Nomor Permohonan</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        {{-- <span class="input-group-text">600.3/</span> --}}
                                        <input type="text" name="var_nomor_permohonan" id="var_nomor_permohonan"
                                            class="form-control  @error('var_nomor_permohonan') is-invalid @enderror"
                                            value="{{ old('var_nomor_permohonan', $permohonan->var_nomor_permohonan ?? '') }}"
                                            placeholder="(Otomatis)">
                                        {{-- <span class="input-group-text">/Permohonan.SITR/427.56/2025</span> --}}
                                    </div>
                                    <small class="form-text text-muted">Jangan ubah kolom ini jika ingin otomatis mengikuti
                                        urutan nomor
                                        surat</small>
                                    @errorFeedback('var_nomor_permohonan')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="date_tanggal_permohonan" class="col-sm-3 col-form-label">Tanggal
                                    Permohonan</label>
                                <div class="col-sm-9">
                                    <input type="date" name="date_tanggal_permohonan" id="date_tanggal_permohonan"
                                        class="form-control  @error('date_tanggal_permohonan') is-invalid @enderror"
                                        value="{{ old('date_tanggal_permohonan', $permohonan->date_tanggal_permohonan ?? '') }}"
                                        required>
                                    @errorFeedback('date_tanggal_permohonan')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_nomor_pengesahan" class="col-sm-3 col-form-label">Nomor Pengesahan</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" name="var_nomor_pengesahan" id="var_nomor_pengesahan"
                                            class="form-control  @error('var_nomor_pengesahan') is-invalid @enderror"
                                            value="{{ old('var_nomor_pengesahan', $permohonan->var_nomor_pengesahan ?? '') }}"
                                            placeholder="(Otomatis)">
                                    </div>
                                    <small class="form-text text-muted">Jangan ubah kolom ini jika ingin otomatis mengikuti
                                        urutan nomor
                                        surat</small>
                                    @errorFeedback('var_nomor_pengesahan')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="date_tanggal_pengesahan" class="col-sm-3 col-form-label">Tanggal
                                    Pengesahan</label>
                                <div class="col-sm-9">
                                    <input type="date" name="date_tanggal_pengesahan" id="date_tanggal_pengesahan"
                                        class="form-control  @error('date_tanggal_pengesahan') is-invalid @enderror"
                                        value="{{ old('date_tanggal_pengesahan', $permohonan->date_tanggal_pengesahan ?? '') }}">
                                    @errorFeedback('date_tanggal_pengesahan')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="pilihan_redaksi_ids" class="col-sm-3 col-form-label">Pilihan Redaksi</label>
                                <div class="col-sm-9">
                                    <select name="pilihan_redaksi_ids[]" id="pilihan_redaksi_ids"
                                        class="form-select select2 @error('pilihan_redaksi_ids') is-invalid @enderror"
                                        multiple>
                                        @php
                                            $selectedRedaksi = old('pilihan_redaksi_ids');
                                            if (is_null($selectedRedaksi) && isset($permohonan)) {
                                                $selectedRedaksi = $permohonan->templateDocs->pluck('id')->toArray();
                                            }
                                        @endphp
                                        @foreach ($templateDocs as $templateDoc)
                                            <option value="{{ $templateDoc->id }}"
                                                {{ is_array($selectedRedaksi) && in_array($templateDoc->id, $selectedRedaksi) ? 'selected' : '' }}>
                                                {{ $templateDoc->var_nama }} ({{ $templateDoc->enum_jenis }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @errorFeedback('pilihan_redaksi_ids')
                                </div>
                            </div>
                            <div class="mb-4">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('permohonan.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
