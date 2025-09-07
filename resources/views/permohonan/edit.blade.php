@extends('layouts.app')

@section('title', 'Edit Permohonan SITR')

@section('page-script')
    <script>
        $('.select2').select2();
    </script>
    <script>
        // Inisialisasi Select2 untuk semua elemen dengan kelas .select2
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('.select2').parent() // Atur parent default
        });

        // --- FUNGSI-FUNGSI UTAMA ---

        /**
         * Fungsi helper untuk me-reload Select2 dengan data baru.
         */
        function reloadSelect2($select, options, placeholder = '', selected = '') {
            $select.html(options).val(selected);
            // Penting: Panggil .trigger('change') setelah semua proses selesai
            // agar event handler tidak terpanggil sebelum waktunya.
            $select.select2({
                width: '100%',
                placeholder: placeholder,
                dropdownParent: $select.parent()
            });
        }

        /**
         * (BARU) Fungsi async untuk mengambil data JSON dan mengisi <select>.
         * Mengembalikan data yang berhasil diambil.
         */
        async function populateSelect($select, url, placeholder, selectedValue = null) {
            // Tampilkan status 'Memuat...' selagi data diambil
            $select.html('<option value="">Memuat...</option>').prop('disabled', true);

            try {
                const data = await $.getJSON(url); // await: tunggu sampai data datang
                let options = `<option value="">${placeholder}</option>`;
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.nama}</option>`;
                });

                reloadSelect2($select, options, placeholder, selectedValue);
                $select.prop('disabled', false);
                return data; // Kembalikan data untuk chaining
            } catch (error) {
                console.error(`Gagal memuat data dari ${url}`, error);
                $select.html(`<option value="">Gagal memuat</option>`).prop('disabled', true);
                throw error; // Lemparkan error agar proses selanjutnya berhenti
            }
        }

        /**
         * (BARU) Fungsi utama yang mengatur pengisian dropdown secara BERURUTAN untuk halaman edit.
         */
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

            // Langkah 1: Muat Provinsi dan TUNGGU sampai selesai
            await populateSelect($prov, '/data-indonesia/provinsi.json', '-- Pilih Provinsi --', prov);

            // Langkah 2: Jika ada ID provinsi, muat Kabupaten dan TUNGGU sampai selesai
            if (prov) {
                await populateSelect($kab, `/data-indonesia/kabupaten/${prov}.json`, '-- Pilih Kabupaten --', kab);
            }

            // Langkah 3: Jika ada ID kabupaten, muat Kecamatan dan TUNGGU sampai selesai
            if (kab) {
                await populateSelect($kec, `/data-indonesia/kecamatan/${kab}.json`, '-- Pilih Kecamatan --', kec);
            }

            // Langkah 4: Jika ada ID kecamatan, muat Kelurahan dan TUNGGU sampai selesai
            if (kec) {
                await populateSelect($kel, `/data-indonesia/kelurahan/${kec}.json`, '-- Pilih Kelurahan --', kel);
            }

            // Langkah 5 (PENTING): Setelah semua dropdown terisi, baru trigger 'change'
            // untuk memastikan event handler mengenali nilai yang sudah terpilih.
            $prov.val(prov).trigger('change.select2');
            $kab.val(kab).trigger('change.select2');
            $kec.val(kec).trigger('change.select2');
            $kel.val(kel).trigger('change.select2');
        }


        // --- EVENT HANDLERS & INISIALISASI ---

        $(document).ready(function() {
            // Ambil data lama (jika ada validation error) atau data dari model
            const pengusulValues = {
                prov: @json(old('var_provinsi', $permohonan->var_provinsi ?? '')),
                kab: @json(old('var_kabupaten', $permohonan->var_kabupaten ?? '')),
                kec: @json(old('var_kecamatan', $permohonan->var_kecamatan ?? '')),
                kel: @json(old('var_kelurahan', $permohonan->var_kelurahan ?? ''))
            };
            const usahaValues = {
                prov: @json(old('var_provinsi_usaha', $permohonan->var_provinsi_usaha ?? '')),
                kab: @json(old('var_kabupaten_usaha', $permohonan->var_kabupaten_usaha ?? '')),
                kec: @json(old('var_kecamatan_usaha', $permohonan->var_kecamatan_usaha ?? '')),
                kel: @json(old('var_kelurahan_usaha', $permohonan->var_kelurahan_usaha ?? ''))
            };

            // Jalankan fungsi inisialisasi untuk setiap grup lokasi
            initializeLocationDropdowns($('.location-group').eq(0), pengusulValues);
            initializeLocationDropdowns($('.location-group').eq(1), usahaValues);

            // --- Event handler untuk interaksi pengguna SETELAH halaman dimuat ---
            $('.location-group').each(function() {
                const $group = $(this);
                const $prov = $group.find('.provinsi');
                const $kab = $group.find('.kabupaten');
                const $kec = $group.find('.kecamatan');
                const $kel = $group.find('.kelurahan');

                $prov.on('change', function() {
                    const provId = $(this).val();
                    $kab.val(null).trigger('change'); // Reset kabupaten
                    $kec.val(null).trigger('change'); // Reset kecamatan
                    $kel.val(null).trigger('change'); // Reset kelurahan

                    if (provId) {
                        populateSelect($kab, `/data-indonesia/kabupaten/${provId}.json`,
                            '-- Pilih Kabupaten --');
                    } else {
                        $kab.html('').prop('disabled', true);
                    }
                    $kec.html('').prop('disabled', true);
                    $kel.html('').prop('disabled', true);
                });

                $kab.on('change', function() {
                    const kabId = $(this).val();
                    $kec.val(null).trigger('change');
                    $kel.val(null).trigger('change');

                    if (kabId) {
                        populateSelect($kec, `/data-indonesia/kecamatan/${kabId}.json`,
                            '-- Pilih Kecamatan --');
                    } else {
                        $kec.html('').prop('disabled', true);
                    }
                    $kel.html('').prop('disabled', true);
                });

                $kec.on('change', function() {
                    const kecId = $(this).val();
                    $kel.val(null).trigger('change');

                    if (kecId) {
                        populateSelect($kel, `/data-indonesia/kelurahan/${kecId}.json`,
                            '-- Pilih Kelurahan --');
                    } else {
                        $kel.html('').prop('disabled', true);
                    }
                });
            });
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBnqQKmS5Q7UhluPg2f1K4gbr_6-KnM3Go&libraries=drawing">
    </script>
    <script>
        setTimeout(function() {
            const map = new google.maps.Map(document.getElementById("map-canvas"), {
                center: {
                    lat: -8.129955181277511,
                    lng: 113.22306606906642
                },
                zoom: 11,
                mapTypeId: "hybrid",
            });

            const drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_LEFT,
                    drawingModes: ["polygon"],
                },
                polygonOptions: {
                    fillColor: "#bada55",
                    fillOpacity: 0.5,
                    strokeWeight: 2,
                    clickable: true,
                    editable: true,
                    zIndex: 1,
                },
            });

            drawingManager.setMap(map);

            let currentPolygon = null;

            // If editing, show existing geometry
            @if (old('json_geometry', $permohonan->json_geometry ?? false))
                let geojson = @json(old('json_geometry', $permohonan->json_geometry));
                if (typeof geojson === 'string') geojson = JSON.parse(geojson);
                if (geojson && geojson.geometry && geojson.geometry.type === "Polygon") {
                    const coords = geojson.geometry.coordinates[0].map(function(coord) {
                        return {
                            lat: coord[1],
                            lng: coord[0]
                        };
                    });
                    currentPolygon = new google.maps.Polygon({
                        paths: coords,
                        fillColor: "#bada55",
                        fillOpacity: 0.5,
                        strokeWeight: 2,
                        editable: true,
                        map: map
                    });
                    // Fit bounds
                    const bounds = new google.maps.LatLngBounds();
                    coords.forEach(p => bounds.extend(p));
                    map.fitBounds(bounds);

                    // Save to hidden input
                    function savePolygon() {
                        const path = currentPolygon.getPath().getArray();
                        const coordinates = path.map(coord => [coord.lng(), coord.lat()]);
                        coordinates.push([path[0].lng(), path[0].lat()]);
                        const geojson = {
                            type: "Feature",
                            geometry: {
                                type: "Polygon",
                                coordinates: [coordinates]
                            },
                            properties: {}
                        };
                        document.getElementById("json_geometry").value = JSON.stringify(geojson);
                    }
                    google.maps.event.addListener(currentPolygon.getPath(), 'set_at', savePolygon);
                    google.maps.event.addListener(currentPolygon.getPath(), 'insert_at', savePolygon);
                    savePolygon();
                }
            @endif

            google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                if (event.type === "polygon") {
                    if (currentPolygon) {
                        currentPolygon.setMap(null);
                    }
                    currentPolygon = event.overlay;
                    // ambil koordinat dari polygon dan ubah ke GeoJSON-like
                    const path = currentPolygon.getPath().getArray();
                    const coordinates = path.map(coord => [coord.lng(), coord.lat()]);
                    coordinates.push([path[0].lng(), path[0].lat()]); // close loop

                    const geojson = {
                        type: "Feature",
                        geometry: {
                            type: "Polygon",
                            coordinates: [coordinates]
                        },
                        properties: {}
                    };

                    document.getElementById("json_geometry").value = JSON.stringify(geojson);

                    // zoom ke area polygon
                    const bounds = new google.maps.LatLngBounds();
                    path.forEach(p => bounds.extend(p));
                    map.fitBounds(bounds);

                    // Listen for edit
                    google.maps.event.addListener(currentPolygon.getPath(), 'set_at', function() {
                        const path = currentPolygon.getPath().getArray();
                        const coordinates = path.map(coord => [coord.lng(), coord.lat()]);
                        coordinates.push([path[0].lng(), path[0].lat()]);
                        const geojson = {
                            type: "Feature",
                            geometry: {
                                type: "Polygon",
                                coordinates: [coordinates]
                            },
                            properties: {}
                        };
                        document.getElementById("json_geometry").value = JSON.stringify(geojson);
                    });
                    google.maps.event.addListener(currentPolygon.getPath(), 'insert_at', function() {
                        const path = currentPolygon.getPath().getArray();
                        const coordinates = path.map(coord => [coord.lng(), coord.lat()]);
                        coordinates.push([path[0].lng(), path[0].lat()]);
                        const geojson = {
                            type: "Feature",
                            geometry: {
                                type: "Polygon",
                                coordinates: [coordinates]
                            },
                            properties: {}
                        };
                        document.getElementById("json_geometry").value = JSON.stringify(geojson);
                    });
                }
            });
        }, 500);
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
                                <div class="mb-4 row">
                                    <label for="var_provinsi_usaha" class="col-sm-3 col-form-label">Provinsi</label>
                                    <div class="col-sm-9">
                                        <select name="var_provinsi_usaha" id="var_provinsi_usaha"
                                            class="form-select select2 provinsi @error('var_provinsi_usaha') is-invalid @enderror">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                        @errorFeedback('var_provinsi_usaha')
                                    </div>
                                </div>

                                <div class="mb-4 row">
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
                            <div id="map-canvas" style="height: 500px; width: 100%; border-radius: 8px;"></div>
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
                                        <span class="input-group-text">600.3/</span>
                                        <input type="text" name="var_nomor_pengesahan" id="var_nomor_pengesahan"
                                            class="form-control  @error('var_nomor_pengesahan') is-invalid @enderror"
                                            value="{{ old('var_nomor_pengesahan', $permohonan->var_nomor_pengesahan ?? '') }}"
                                            placeholder="(Otomatis)">
                                        <span class="input-group-text">/ITR/427.56/2025</span>
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
                                        value="{{ old('date_tanggal_pengesahan', $permohonan->date_tanggal_pengesahan ?? '') }}"
                                        required>
                                    @errorFeedback('date_tanggal_pengesahan')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label class="col-sm-3 col-form-label">Pilihan Redaksi</label>
                                <div class="col-sm-9 d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="json_pilihan_redaksi[]"
                                            id="redaksi_sitr" value="SITR"
                                            {{ is_array(old('json_pilihan_redaksi', $permohonan->json_pilihan_redaksi ?? [])) && in_array('SITR', old('json_pilihan_redaksi', $permohonan->json_pilihan_redaksi ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="redaksi_sitr">SITR</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="json_pilihan_redaksi[]"
                                            id="redaksi_rdtr" value="RDTR"
                                            {{ is_array(old('json_pilihan_redaksi', $permohonan->json_pilihan_redaksi ?? [])) && in_array('RDTR', old('json_pilihan_redaksi', $permohonan->json_pilihan_redaksi ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="redaksi_rdtr">RDTR</label>
                                    </div>
                                    @errorFeedback('json_pilihan_redaksi')
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
