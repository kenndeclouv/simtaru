<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanRequest;
use App\Models\KeyStorage;
use App\Models\Permohonan;
use App\Models\PermohonanTemplateDoc;
use App\Models\TemplateDocs;
use Illuminate\Http\Request;
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

        // Convert json_pilihan_redaksi array to string (JSON)
        if (isset($validated['json_pilihan_redaksi']) && is_array($validated['json_pilihan_redaksi'])) {
            $validated['json_pilihan_redaksi'] = json_encode($validated['json_pilihan_redaksi']);
        }

        $permohonan->update($validated);

        return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil diperbarui.');
    }


    public function show(Permohonan $permohonan)
    {
        $templateDocs = TemplateDocs::all();
        return view('permohonan.show', compact('permohonan', 'templateDocs'));
    }
}
