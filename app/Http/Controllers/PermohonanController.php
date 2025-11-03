<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanRequest;
use App\Models\District;
use App\Models\KeyStorage;
use App\Models\Permohonan;
use App\Models\PermohonanTemplateDoc;
use App\Models\Province;
use App\Models\Regency;
use App\Models\TemplateDocs;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use geoPHP;

class PermohonanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->authorizeResource(Permohonan::class, 'permohonan');
    }

    public function index(Request $request)
    {
        $type = $request->type ?? 'sitr/rdtr';
        if ($request->ajax()) {
            $query = Permohonan::with('permohonanTemplateDocs.templateDocs')
                ->where('var_type', $type);

            $user = Auth::user();

            if (!$user->can('view any permohonan') && $user->can('view permohonan')) {
                $query->where('user_id', $user->id);
            }
            else if (!$user->can('view any permohonan') && !$user->can('view permohonan')) {
                $query->where('id', 0);
            }

            $query->orderBy('created_at', 'desc');
            return DataTables::of($query)->make(true);
        }
        return view('permohonan.index', compact('type'));
    }

    public function create(Request $request)
    {
        $type = $request->type ?? 'sitr/rdtr';
        $keyStorages = KeyStorage::all();
        $user = Auth::user();
        $apiToken = $user->createToken('form-upload-token')->plainTextToken;
        if ($type == 'sitr/rdtr') {
            $templateDocs = TemplateDocs::where('enum_jenis', 'sitr')->orWhere('enum_jenis', 'rdtr')->get();
        } else {
            $templateDocs = TemplateDocs::where('enum_jenis', $type)->get();
        }
        return view('permohonan.create', compact('templateDocs', 'keyStorages', 'type', 'apiToken'));
    }

    public function store(PermohonanRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        $tahun = now()->year;
        $last = (Permohonan::latest()->first()->id ?? 0) + 1;
        $preFixNomorPermohonan = KeyStorage::where('var_key', 'preFixNomorPermohonan')->first()->var_value;
        $postFixNomorPermohonan = KeyStorage::where('var_key', 'postFixNomorPermohonan')->first()->var_value;
        $preFixNomorSurat = KeyStorage::where('var_key', 'preFixNomorSurat')->first()->var_value;
        $postFixNomorSurat = KeyStorage::where('var_key', 'postFixNomorSurat')->first()->var_value;

        $validated["user_id"] = $user->id;
        $validated['var_nomor_permohonan'] = "{$preFixNomorPermohonan}{$last}{$postFixNomorPermohonan}{$tahun}";
        $validated['var_nomor_pengesahan'] = "{$preFixNomorSurat}{$last}{$postFixNomorSurat}{$tahun}";
        $validated['var_type'] = $validated['var_type'] ?? 'sitr/rdtr';

        $permohonan = Permohonan::create($validated);

        $attachmentFields = [
            'var_fotocopy_ktp_attachment',
            'var_fotocopy_npwp_attachment',
            'var_foto_lokasi_rencana_kegiatan_attachment',
            'var_titik_koordinat_attachment',
            'var_sitr_attachment',
            'var_lp2b_attachment',
            'var_bukti_penguasaan_tanah_attachment',
            'var_rencana_teknis_bangunan_attachment',
            'var_ptp_kkpr_nonberusaha_attachment',
            'var_akta_pendirian_badan_attachment',
        ];

        $finalAttachmentPaths = [];

        foreach ($attachmentFields as $field) {
            $tempPath = $validated[$field] ?? null;
            if ($tempPath && Storage::disk('public')->exists($tempPath)) {
                $fileName = basename($tempPath);
                $permanentPath = "permohonan/{$permohonan->id}/{$field}/{$fileName}";
                Storage::disk('public')->move($tempPath, $permanentPath);
                $finalAttachmentPaths[$field] = $permanentPath;
            }
        }


        if (!empty($finalAttachmentPaths)) {
            $permohonan->update($finalAttachmentPaths);
        }


        if (isset($validated['pilihan_redaksi_ids'])) {
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

        return redirect()->route('permohonan.index', ['type' => $validated['var_type']])->with('success', 'Permohonan berhasil disimpan.');
    }

    public function edit(Permohonan $permohonan, Request $request)
    {
        $type = $request->type ?? 'sitr/rdtr';
        $keyStorages = KeyStorage::all();
        $user = Auth::user();
        $apiToken = $user->createToken('form-upload-token')->plainTextToken;
        if ($type == 'sitr/rdtr') {
            $templateDocs = TemplateDocs::where('enum_jenis', 'sitr')->orWhere('enum_jenis', 'rdtr')->get();
        } else {
            $templateDocs = TemplateDocs::where('enum_jenis', $type)->get();
        }
        return view('permohonan.edit', compact('permohonan', 'templateDocs', 'keyStorages', 'type', 'apiToken'));
    }

    public function update(PermohonanRequest $request, Permohonan $permohonan)
    {
        $validated = $request->validated();


        $attachmentFields = [
            'var_fotocopy_ktp_attachment',
            'var_fotocopy_npwp_attachment',
            'var_foto_lokasi_rencana_kegiatan_attachment',
            'var_titik_koordinat_attachment',
            'var_sitr_attachment',
            'var_lp2b_attachment',
            'var_bukti_penguasaan_tanah_attachment',
            'var_rencana_teknis_bangunan_attachment',
            'var_ptp_kkpr_nonberusaha_attachment',
            'var_akta_pendirian_badan_attachment',
        ];

        $finalAttachmentPaths = [];

        foreach ($attachmentFields as $field) {
            $tempPath = $validated[$field] ?? null;
            if ($tempPath && Storage::disk('public')->exists($tempPath)) {

                if ($permohonan->$field && Storage::disk('public')->exists($permohonan->$field)) {
                    Storage::disk('public')->delete($permohonan->$field);
                }

                $fileName = basename($tempPath);
                $permanentPath = "permohonan/{$permohonan->id}/{$field}/{$fileName}";
                Storage::disk('public')->move($tempPath, $permanentPath);
                $finalAttachmentPaths[$field] = $permanentPath;
            }
        }


        $updateData = array_merge($validated, $finalAttachmentPaths);
        $permohonan->update($updateData);


        if (isset($validated['pilihan_redaksi_ids'])) {
            $permohonan->permohonanTemplateDocs()->delete();
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

        return redirect()->route('permohonan.index', ['type' => $permohonan->var_type])->with('success', 'Permohonan berhasil diperbarui.');
    }

    public function show($id)
    {
        $permohonan = Permohonan::with('permohonanTemplateDocs.templateDocs')
        ->findOrFail($id);

        return view('permohonan.show', [
            'permohonan' => $permohonan,
        ]);
    }

    public function destroy(Permohonan $permohonan)
    {
        $permohonan->delete();
        return redirect()->back()->with('success', 'Permohonan berhasil dihapus.');
    }

    public function status(Permohonan $permohonan, Request $request)
    {
        $this->authorize('approve', $permohonan);

        $lampiranName = null;
        if ($request->hasFile('lampiran')) {
            $lampiran = $request->file('lampiran');
            $lampiranName = time() . '_' . $lampiran->getClientOriginalName();
            $lampiran->storeAs('uploads', $lampiranName);
        }

        $updateData = [
            'enum_status' => $request->status,
            'text_catatan' => $request->catatan,
        ];

        if ($lampiranName) {
            $updateData['var_lampiran'] = $lampiranName;
        }

        if ($request->status === 'request_tte') {
            $updateData['user_request_tte_id'] = Auth::user()->id;
            $updateData['request_tte_date'] = now();
            $this->generateDocuments($permohonan);
        } else if ($request->status === 'rejected') {
            $updateData['user_request_tte_id'] = null;
            $updateData['request_tte_date'] = null;
        } else if ($request->status === 'approved') {
            $updateData['approved_date'] = now();
        }

        $permohonan->update($updateData);

        return redirect()->back()->with('success', 'Status permohonan berhasil diubah.');
    }

    public function generateDocuments(Permohonan $permohonan)
    {
        $this->authorize('update', $permohonan);

        $permohonanTemplateDocs = $permohonan->permohonanTemplateDocs()->with('templateDocs')->get();

        $replacementData = $permohonan->getAttributes();

        $replacementData['var_provinsi'] = $permohonan->nama_provinsi;
        $replacementData['var_kabupaten'] = $permohonan->nama_kabupaten;
        $replacementData['var_kecamatan'] = $permohonan->nama_kecamatan;
        $replacementData['var_kelurahan'] = $permohonan->nama_kelurahan;
        $replacementData['var_kecamatan_usaha'] = $permohonan->nama_kecamatan_usaha;
        $replacementData['var_kelurahan_usaha'] = $permohonan->nama_kelurahan_usaha;

        unset($replacementData['json_geometry']);


        $koordinatList = $permohonan->koordinat;

        $tableValues = [];
        if (!empty($koordinatList)) {
            foreach ($koordinatList as $index => $koor) {
                $tableValues[] = [
                    'koor_no'  => $index + 1,
                    'koor_lng' => $koor[0] ?? 'N/A',
                    'koor_lat' => $koor[1] ?? 'N/A',
                ];
            }
        } else {
            $tableValues[] = ['koor_no' => 'N/A', 'koor_lng' => 'N/A', 'koor_lat' => 'N/A'];
        }

        foreach ($permohonanTemplateDocs as $permohonanTemplateDoc) {
            $template = $permohonanTemplateDoc->templateDocs;

            if (!$template) {
                Log::error("Data TemplateDocs tidak ditemukan untuk relasi ID: {$permohonanTemplateDoc->id}");
                continue;
            }

            try {
                $templatePath = Storage::disk('public')->path($template->var_file_path);

                if (!file_exists($templatePath)) {
                    Log::error("File template tidak ditemukan: {$templatePath}");
                    continue;
                }

                $templateProcessor = new TemplateProcessor($templatePath);


                $templateProcessor->setValues($replacementData);


                $templateProcessor->cloneRowAndSetValues('koor_no', $tableValues);


                $generatedDir = "generated_documents/{$permohonan->id}";
                $newFileName = pathinfo($template->var_file_path, PATHINFO_FILENAME) . '_' . time() . '.docx';
                $newFilePath = "{$generatedDir}/{$newFileName}";

                $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
                $templateProcessor->saveAs($tempFile);
                Storage::disk('public')->put($newFilePath, file_get_contents($tempFile));
                unlink($tempFile);

                $permohonanTemplateDoc->update([
                    'var_generated_file_path' => $newFilePath
                ]);
            } catch (Exception $e) {
                Log::error('Gagal generate dokumen: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal generate: ' . $template->var_nama . ' - ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Semua dokumen berhasil di-generate!');
    }

    public function downloadKml(Permohonan $permohonan)
    {
        if (!$permohonan->json_geometry) {
            return redirect()->back()->with('error', 'Data lokasi tidak ditemukan.');
        }

        try {

            $geojson = geoPHP::load($permohonan->json_geometry, 'json');


            $geometryFragment = $geojson->out('kml');


            $fullKml = <<<KML
                <?xml version="1.0" encoding="UTF-8"?>
                <kml xmlns="http://www.opengis.net/kml/2.2">
                  <Placemark>
                    <name>{$permohonan->var_nama_usaha}</name>
                    <description>Lokasi untuk permohonan {$permohonan->var_nomor_permohonan}</description>
                    {$geometryFragment}
                  </Placemark>
                </kml>
                KML;


            $fileName = 'lokasi_' . preg_replace('/[^A-Za-z0-9\-]/', '', $permohonan->var_nama_usaha) . '.kml';


            return response($fullKml, 200, [
                'Content-Type' => 'application/vnd.google-earth.kml+xml',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (Exception $e) {
            Log::error('KML Generation Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat file KML dari data lokasi.');
        }
    }
}
