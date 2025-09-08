<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanRequest;
use App\Models\KeyStorage;
use App\Models\Permohonan;
use App\Models\PermohonanTemplateDoc;
use App\Models\TemplateDocs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class PermohonanController extends Controller
{
    /**
     * Terapkan middleware permission di sini.
     */
    public function __construct()
    {
        // User harus login untuk semua method
        $this->middleware('auth');

        // Terapkan permission spesifik untuk setiap method
        $this->middleware('can:view permohonan')->only(['index', 'show']);
        $this->middleware('can:create permohonan')->only(['create', 'store']);
        $this->middleware('can:edit permohonan')->only(['edit', 'update']);
        $this->middleware('can:delete permohonan')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Permohonan::query();
            return DataTables::of($query)
                ->addColumn('var_kabupaten', function ($row) {
                    // Pastikan $row->var_kabupaten tidak kosong
                    if (empty($row->var_kabupaten)) {
                        return '-';
                    }

                    // 1. Ambil ID Kabupaten dari baris data
                    $kabupatenId = $row->var_kabupaten;

                    // 2. Ekstrak ID Provinsi (2 digit pertama)
                    $provinsiId = substr($kabupatenId, 0, 2);

                    // 3. Panggil helper untuk mengambil semua data kabupaten di provinsi itu
                    $semuaKabupaten = getKabupaten($provinsiId);

                    // 4. Cari nama kabupaten yang cocok berdasarkan ID
                    $namaKabupaten = collect($semuaKabupaten)->firstWhere('id', $kabupatenId)['nama'] ?? '(Tidak Ditemukan)';

                    return $namaKabupaten;
                })
                ->addColumn('status', function ($row) {
                    return $row->var_nomor_pengesahan
                        ? '<span class="badge bg-success">Selesai</span>'
                        : '<span class="badge bg-warning">Diproses</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
        return view('permohonan.index');
    }

    public function create()
    {
        $keyStorages = KeyStorage::all();
        $templateDocs = TemplateDocs::all();
        return view('permohonan.create', compact('templateDocs', 'keyStorages'));
    }

    public function store(PermohonanRequest $request)
    {
        $validated = $request->validated();

        // dd($validated);
        // generate nomor permohonan otomatis
        $tahun = now()->year;
        $last = Permohonan::latest()->first()->id ?? 0 + 1;
        $preFixNomorPermohonan = KeyStorage::where('var_key', 'preFixNomorPermohonan')->first()->var_value;
        $postFixNomorPermohonan = KeyStorage::where('var_key', 'postFixNomorPermohonan')->first()->var_value;
        $preFixNomorSurat = KeyStorage::where('var_key', 'preFixNomorSurat')->first()->var_value;
        $postFixNomorSurat = KeyStorage::where('var_key', 'postFixNomorSurat')->first()->var_value;

        $validated['var_nomor_permohonan'] = "{$preFixNomorPermohonan}{$last}{$postFixNomorPermohonan}{$tahun}";
        $validated['var_nomor_pengesahan'] = "{$preFixNomorSurat}{$last}{$postFixNomorSurat}{$tahun}";

        $permohonan = Permohonan::create($validated);

        $pilihan_redaksi_ids = $validated['pilihan_redaksi_ids'];
        foreach ($pilihan_redaksi_ids as $template_doc_id) {
            $template_doc = TemplateDocs::find($template_doc_id);
            PermohonanTemplateDoc::create([
                'fk_permohonan_id' => $permohonan->id,
                'fk_template_docs_id' => $template_doc->id
            ]);
        }

        return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil disimpan.');
    }

    public function edit(Permohonan $permohonan)
    {
        $templateDocs = TemplateDocs::all();
        return view('permohonan.edit', compact('permohonan', 'templateDocs'));
    }

    public function update(PermohonanRequest $request, Permohonan $permohonan)
    {
        $validated = $request->validated();

        $permohonan->update($validated);

        // Update relasi PermohonanTemplateDoc
        if (isset($validated['pilihan_redaksi_ids'])) {
            // Hapus relasi lama
            $permohonan->templateDocs()->detach();
            // Tambah relasi baru
            foreach ($validated['pilihan_redaksi_ids'] as $template_doc_id) {
                $template_doc = TemplateDocs::find($template_doc_id);
                if ($template_doc) {
                    PermohonanTemplateDoc::create([
                        'fk_permohonan_id' => $permohonan->id,
                        'fk_template_docs_id' => $template_doc->id
                    ]);
                }
            }
        }

        return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil diperbarui.');
    }

    public function show($id)
    {
        $permohonan = Permohonan::findOrFail($id);

        // Array untuk menampung nama-nama wilayah
        $namaWilayah = [];

        // --- Cari nama untuk ALAMAT PENGUSUL ---
        if ($permohonan->var_provinsi) {
            $namaWilayah['provinsi'] = $this->findWilayahNameById('data-indonesia/provinsi.json', $permohonan->var_provinsi);
        }
        if ($permohonan->var_kabupaten) {
            $namaWilayah['kabupaten'] = $this->findWilayahNameById("data-indonesia/kabupaten/{$permohonan->var_provinsi}.json", $permohonan->var_kabupaten);
        }
        if ($permohonan->var_kecamatan) {
            $namaWilayah['kecamatan'] = $this->findWilayahNameById("data-indonesia/kecamatan/{$permohonan->var_kabupaten}.json", $permohonan->var_kecamatan);
        }
        if ($permohonan->var_kelurahan) {
            $namaWilayah['kelurahan'] = $this->findWilayahNameById("data-indonesia/kelurahan/{$permohonan->var_kecamatan}.json", $permohonan->var_kelurahan);
        }

        // --- Cari nama untuk ALAMAT USAHA ---
        if ($permohonan->var_kecamatan_usaha) {
            // Asumsi `var_kabupaten_usaha` juga tersimpan dari form edit/create
            $namaWilayah['kecamatan_usaha'] = $this->findWilayahNameById("data-indonesia/kecamatan/{$permohonan->var_kabupaten_usaha}.json", $permohonan->var_kecamatan_usaha);
        }
        if ($permohonan->var_kelurahan_usaha) {
            $namaWilayah['kelurahan_usaha'] = $this->findWilayahNameById("data-indonesia/kelurahan/{$permohonan->var_kecamatan_usaha}.json", $permohonan->var_kelurahan_usaha);
        }

        return view('permohonan.show', [
            'permohonan' => $permohonan,
            'namaWilayah' => $namaWilayah, // Kirim data nama wilayah ke view
        ]);
    }

    // destroy
    public function destroy(Permohonan $permohonan)
    {
        $permohonan->delete();
        return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil dihapus.');
    }

    // approve
    public function status(Permohonan $permohonan, Request $request)
    {

        // upload lampiran
        $lampiranName = null;
        if ($request->hasFile('var_lampiran')) {
            $lampiran = $request->file('var_lampiran');
            $lampiranName = time() . '_' . $lampiran->getClientOriginalName();
            $lampiran->storeAs('uploads', $lampiranName);
        }

        $permohonan->update([
            'enum_status' => $request->status,
            'date_tanggal_pengesahan' => now(),
            'text_catatan' => $request->text_catatan,
            'var_lampiran' => $lampiranName,
        ]);
        return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil diubah status menjadi ' . $request->status . '.');
    }

    /**
     * Fungsi helper untuk mencari nama wilayah dari file JSON.
     */
    private function findWilayahNameById($filePath, $id)
    {
        $fullPath = public_path($filePath);

        if (!File::exists($fullPath)) {
            return $id; // Jika file tidak ada, kembalikan ID-nya saja
        }

        $data = json_decode(File::get($fullPath), true);

        // Cari array yang cocok berdasarkan ID
        $found = collect($data)->firstWhere('id', $id);

        return $found ? $found['nama'] : $id; // Jika ketemu, kembalikan 'nama', jika tidak, kembalikan ID
    }
}
