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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class PermohonanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

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
                    if (empty($row->var_kabupaten)) {
                        return '-';
                    }

                    $kabupatenId = $row->var_kabupaten;

                    $provinsiId = substr($kabupatenId, 0, 2);

                    $semuaKabupaten = getKabupaten($provinsiId);

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
        $keyStorages = KeyStorage::all();
        return view('permohonan.edit', compact('permohonan', 'templateDocs', 'keyStorages'));
    }

    public function update(PermohonanRequest $request, Permohonan $permohonan)
    {
        $validated = $request->validated();

        $permohonan->update($validated);

        if (isset($validated['pilihan_redaksi_ids'])) {
            $permohonan->templateDocs()->detach();
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


        return view('permohonan.show', [
            'permohonan' => $permohonan,
        ]);
    }

    public function destroy(Permohonan $permohonan)
    {
        $permohonan->delete();
        return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil dihapus.');
    }

    public function status(Permohonan $permohonan, Request $request)
    {

        $lampiranName = null;
        if ($request->hasFile('lampiran')) {
            $lampiran = $request->file('lampiran');
            $lampiranName = time() . '_' . $lampiran->getClientOriginalName();
            $lampiran->storeAs('uploads', $lampiranName);
        }

        $permohonan->update([
            'enum_status' => $request->status,
            'date_tanggal_pengesahan' => $request->status === 'approved' ? now() : null,
            'text_catatan' => $request->catatan,
            'var_lampiran' => $lampiranName,
        ]);
        return redirect()->route('permohonan.index')->with('success', 'Status permohonan berhasil diubah.');
    }

    public function generateDocuments(Permohonan $permohonan)
    {
        $templates = $permohonan->templateDocs;

        $replacementData = $permohonan->toArray();

        $replacementData['var_provinsi'] = $permohonan->nama_provinsi;
        $replacementData['var_kabupaten'] = $permohonan->nama_kabupaten;
        $replacementData['var_kecamatan'] = $permohonan->nama_kecamatan;
        $replacementData['var_kelurahan'] = $permohonan->nama_kelurahan;
        $replacementData['var_kecamatan_usaha'] = $permohonan->nama_kecamatan_usaha;
        $replacementData['var_kelurahan_usaha'] = $permohonan->nama_kelurahan_usaha;


        foreach ($templates as $template) {
            try {
                $templatePath = Storage::disk('public')->path($template->var_file_path);

                if (!file_exists($templatePath)) {
                    Log::error("File template tidak ditemukan: {$templatePath}");
                    continue;
                }

                $templateProcessor = new TemplateProcessor($templatePath);
                $placeholders = $template->placeholders->pluck('var_key')->toArray();

                $valuesToSet = [];
                foreach ($placeholders as $key) {
                    $valuesToSet[$key] = $replacementData[$key] ?? '';
                }

                $templateProcessor->setValues($valuesToSet);

                $generatedDir = "generated_documents/{$permohonan->id}";
                $newFileName = pathinfo($template->var_file_path, PATHINFO_FILENAME) . '_' . time() . '.docx';
                $newFilePath = "{$generatedDir}/{$newFileName}";

                $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
                $templateProcessor->saveAs($tempFile);
                Storage::disk('public')->put($newFilePath, file_get_contents($tempFile));
                unlink($tempFile);

                $permohonan->templateDocs()->updateExistingPivot($template->id, [
                    'var_generated_file_path' => $newFilePath
                ]);
            } catch (Exception $e) {
                Log::error("Gagal generate dokumen: " . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal generate: ' . $template->var_nama . ' - ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Semua dokumen berhasil di-generate!');
    }
}
