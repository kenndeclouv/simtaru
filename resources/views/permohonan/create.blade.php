@extends('layouts.app')

@section('title', 'Tambahkan Permohonan SITR')

@section('page-script')
    <script>
        $('.select2').select2();
    </script>
    <script>
        function reloadSelect2($select, options, placeholder = '') {
            $select.html(options).val('').trigger('change');
            $select.select2({
                // theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                dropdownParent: $select.parent()
            });
        }


        $(document).ready(function() {
            // Load provinsi sekali untuk semua group
            $.getJSON('/data-indonesia/provinsi.json', function(data) {
                let opt = '<option value="">-- Pilih Provinsi --</option>';
                data.forEach(p => opt += `<option value="${p.id}">${p.nama}</option>`);
                $('.provinsi').each(function() {
                    reloadSelect2($(this), opt, '-- Pilih Provinsi --');
                });
            });

            // Event binding per group
            $('.location-group').each(function() {
                const $group = $(this);
                const $prov = $group.find('.provinsi');
                const $kab = $group.find('.kabupaten');
                const $kec = $group.find('.kecamatan');
                const $kel = $group.find('.kelurahan');

                // Provinsi change
                $prov.on('change', function() {
                    let id = $(this).val();

                    reloadSelect2($kab, '<option value="">-- Pilih Kabupaten --</option>',
                        '-- Pilih Kabupaten --');
                    reloadSelect2($kec, '<option value="">-- Pilih Kecamatan --</option>',
                        '-- Pilih Kecamatan --');
                    reloadSelect2($kel, '<option value="">-- Pilih Kelurahan --</option>',
                        '-- Pilih Kelurahan --');

                    $kab.prop('disabled', !id);
                    $kec.prop('disabled', true);
                    $kel.prop('disabled', true);

                    if (id) {
                        $.getJSON(`/data-indonesia/kabupaten/${id}.json`, function(data) {
                            let opt = '<option value="">-- Pilih Kabupaten --</option>';
                            data.forEach(k => opt +=
                                `<option value="${k.id}">${k.nama}</option>`);
                            reloadSelect2($kab, opt, '-- Pilih Kabupaten --');
                        });
                    }
                });

                // Kabupaten change
                $kab.on('change', function() {
                    let id = $(this).val();

                    reloadSelect2($kec, '<option value="">-- Pilih Kecamatan --</option>',
                        '-- Pilih Kecamatan --');
                    reloadSelect2($kel, '<option value="">-- Pilih Kelurahan --</option>',
                        '-- Pilih Kelurahan --');

                    $kec.prop('disabled', !id);
                    $kel.prop('disabled', true);

                    if (id) {
                        $.getJSON(`/data-indonesia/kecamatan/${id}.json`, function(data) {
                            let opt = '<option value="">-- Pilih Kecamatan --</option>';
                            data.forEach(k => opt +=
                                `<option value="${k.id}">${k.nama}</option>`);
                            reloadSelect2($kec, opt, '-- Pilih Kecamatan --');
                        });
                    }
                });

                // Kecamatan change
                $kec.on('change', function() {
                    let id = $(this).val();

                    reloadSelect2($kel, '<option value="">-- Pilih Kelurahan --</option>',
                        '-- Pilih Kelurahan --');
                    $kel.prop('disabled', !id);

                    if (id) {
                        $.getJSON(`/data-indonesia/kelurahan/${id}.json`, function(data) {
                            let opt = '<option value="">-- Pilih Kelurahan --</option>';
                            data.forEach(k => opt +=
                                `<option value="${k.id}">${k.nama}</option>`);
                            reloadSelect2($kel, opt, '-- Pilih Kelurahan --');
                        });
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
                drawingMode: null,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_LEFT,
                    drawingModes: ["marker", "polyline", "polygon"],
                },
                markerOptions: {
                    draggable: true,
                },
                polylineOptions: {
                    strokeColor: "#DAD155",
                    strokeOpacity: 1.0,
                    strokeWeight: 3,
                    editable: true,
                },
                polygonOptions: {
                    fillColor: "#DAD155",
                    fillOpacity: 0.5,
                    strokeWeight: 2,
                    clickable: true,
                    editable: true,
                    zIndex: 1,
                },
            });

            drawingManager.setMap(map);

            let currentOverlay = null;

            google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                // Remove previous overlay if exists
                if (currentOverlay) {
                    currentOverlay.setMap(null);
                }
                currentOverlay = event.overlay;

                let geojson = null;

                if (event.type === "marker") {
                    const position = event.overlay.getPosition();
                    geojson = {
                        type: "Feature",
                        geometry: {
                            type: "Point",
                            coordinates: [position.lng(), position.lat()]
                        },
                        properties: {}
                    };
                } else if (event.type === "polyline") {
                    const path = event.overlay.getPath().getArray();
                    const coordinates = path.map(coord => [coord.lng(), coord.lat()]);
                    geojson = {
                        type: "Feature",
                        geometry: {
                            type: "LineString",
                            coordinates: coordinates
                        },
                        properties: {}
                    };
                } else if (event.type === "polygon") {
                    const path = event.overlay.getPath().getArray();
                    const coordinates = path.map(coord => [coord.lng(), coord.lat()]);
                    coordinates.push([path[0].lng(), path[0].lat()]); // close loop
                    geojson = {
                        type: "Feature",
                        geometry: {
                            type: "Polygon",
                            coordinates: [coordinates]
                        },
                        properties: {}
                    };
                }

                // simpan ke hidden input
                document.getElementById("json_geometry").value = JSON.stringify(geojson);

                // zoom ke area overlay
                if (event.type === "marker") {
                    map.setCenter(event.overlay.getPosition());
                    map.setZoom(16);
                } else {
                    const bounds = new google.maps.LatLngBounds();
                    if (event.type === "polyline" || event.type === "polygon") {
                        event.overlay.getPath().forEach(p => bounds.extend(p));
                        map.fitBounds(bounds);
                    }
                }
            });
        }, 500);
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumb :items="[['text' => 'Permohonan SITR', 'url' => route('permohonan.index')], ['text' => 'Tambah Permohonan']]" />

        <form action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
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
                                        value="{{ old('var_nik') }}" maxlength="16" required>
                                    @errorFeedback('var_nik')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_nama" class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_nama" id="var_nama"
                                        class="form-control  @error('var_nama') is-invalid @enderror"
                                        value="{{ old('var_nama') }}" required>
                                    @errorFeedback('var_nama')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="text_alamat" class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9">
                                    <textarea name="text_alamat" id="text_alamat" class="form-control  @error('text_alamat') is-invalid @enderror"
                                        rows="3" required>{{ old('text_alamat') }}</textarea>
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
                                        value="{{ old('var_email') }}">
                                    @errorFeedback('var_email')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_no_telp" class="col-sm-3 col-form-label">No. Telp</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_no_telp" id="var_no_telp"
                                        class="form-control  @error('var_no_telp') is-invalid @enderror phone-mask"
                                        value="{{ old('var_no_telp') }}" required>
                                    @errorFeedback('var_no_telp')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_no_ponsel" class="col-sm-3 col-form-label">No. Ponsel</label>
                                <div class="col-sm-9">
                                    <input type="text" name="var_no_ponsel" id="var_no_ponsel"
                                        class="form-control  @error('var_no_ponsel') is-invalid @enderror phone-mask"
                                        value="{{ old('var_no_ponsel') }}" required>
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
                                        value="{{ old('var_nama_usaha') }}" required>
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
                                            {{ old('var_bentuk_usaha') == 'Perorangan' ? 'selected' : '' }}>Perorangan
                                        </option>
                                        <option value="CV/UD" {{ old('var_bentuk_usaha') == 'CV/UD' ? 'selected' : '' }}>
                                            CV/UD</option>
                                        <option value="PT" {{ old('var_bentuk_usaha') == 'PT' ? 'selected' : '' }}>PT
                                        </option>
                                        <option value="BUMDES/BUMDESMa"
                                            {{ old('var_bentuk_usaha') == 'BUMDES/BUMDESMa' ? 'selected' : '' }}>
                                            BUMDES/BUMDESMa</option>
                                        <option value="Yayasan"
                                            {{ old('var_bentuk_usaha') == 'Yayasan' ? 'selected' : '' }}>
                                            Yayasan</option>
                                        <option value="Lainnya/Instansi"
                                            {{ old('var_bentuk_usaha') == 'Lainnya/Instansi' ? 'selected' : '' }}>
                                            Lainnya/Instansi</option>
                                    </select>
                                    @errorFeedback('var_bentuk_usaha')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="text_alamat_usaha" class="col-sm-3 col-form-label">Alamat Usaha</label>
                                <div class="col-sm-9">
                                    <textarea name="text_alamat_usaha" id="text_alamat_usaha"
                                        class="form-control  @error('text_alamat_usaha') is-invalid @enderror" rows="3" required>{{ old('text_alamat_usaha') }}</textarea>
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
                                        class="form-control  @error('var_rencana_usaha') is-invalid @enderror" required>{{ old('var_rencana_usaha') }}</textarea>
                                    @errorFeedback('var_rencana_usaha')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="dec_rencana_luas_lantai" class="col-sm-3 col-form-label">Rencana Luas
                                    Lantai</label>
                                <div class="col-sm-9">
                                    <input type="number" name="dec_rencana_luas_lantai" id="dec_rencana_luas_lantai"
                                        class="form-control  @error('dec_rencana_luas_lantai') is-invalid @enderror"
                                        value="{{ old('dec_rencana_luas_lantai') }}" required>
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
                            <input type="hidden" name="json_geometry" id="json_geometry">
                            <div class="my-4 row">
                                <label for="nomor_permohonan" class="col-sm-3 col-form-label">Nomor Permohonan</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $keyStorages->where('var_key', 'preFixNomorPermohonan')->first()->var_value }}</span>
                                        <input type="text" name="var_nomor_permohonan" id="var_nomor_permohonan"
                                            class="form-control  @error('var_nomor_permohonan') is-invalid @enderror"
                                            placeholder="(Otomatis)">
                                        <span class="input-group-text">{{ $keyStorages->where('var_key', 'postFixNomorPermohonan')->first()->var_value }}</span>
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
                                        value="2025-08-07" required>
                                    @errorFeedback('date_tanggal_permohonan')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="var_nomor_pengesahan" class="col-sm-3 col-form-label">Nomor Pengesahan</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $keyStorages->where('var_key', 'preFixNomorSurat')->first()->var_value }}</span>
                                        <input type="text" name="var_nomor_pengesahan" id="var_nomor_pengesahan"
                                            class="form-control  @error('var_nomor_pengesahan') is-invalid @enderror"
                                            placeholder="(Otomatis)">
                                        <span class="input-group-text">{{ $keyStorages->where('var_key', 'postFixNomorSurat')->first()->var_value }}</span>
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
                                        class="form-control  @error('date_tanggal_pengesahan') is-invalid @enderror">
                                    @errorFeedback('date_tanggal_pengesahan')
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="pilihan_redaksi_ids" class="col-sm-3 col-form-label">Pilihan Redaksi</label>
                                <div class="col-sm-9">
                                    <select name="pilihan_redaksi_ids[]" id="pilihan_redaksi_ids"
                                        class="form-select select2 @error('pilihan_redaksi_ids') is-invalid @enderror"
                                        multiple>
                                        {{-- <option value="SITR" {{ is_array(old('pilihan_redaksi_ids')) && in_array('SITR', old('pilihan_redaksi_ids')) ? 'selected' : '' }}>SITR</option>
                                        <option value="RDTR" {{ is_array(old('pilihan_redaksi_ids')) && in_array('RDTR', old('pilihan_redaksi_ids')) ? 'selected' : '' }}>RDTR</option> --}}
                                        @foreach ($templateDocs as $templateDoc)
                                            <option value="{{ $templateDoc->id }}"
                                                {{ is_array(old('pilihan_redaksi_ids')) && in_array($templateDoc->id, old('pilihan_redaksi_ids')) ? 'selected' : '' }}>
                                                {{ $templateDoc->var_nama }} ({{ $templateDoc->enum_jenis }})</option>
                                        @endforeach
                                    </select>
                                    {{-- <small class="form-text text-muted">Tekan Ctrl (atau Cmd di Mac) untuk memilih lebih
                                        dari satu.</small> --}}
                                    @errorFeedback('pilihan_redaksi_ids')
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ route('permohonan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </div>


            </div>
        </form>
    </div>
@endsection
