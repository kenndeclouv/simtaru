<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Http\Resources\PermohonanResource;
use App\Models\KeyStorage;
use App\Models\Permohonan;
use App\Models\PermohonanTemplateDoc;
use App\Models\TemplateDocs;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ManagesPermohonan
{
    public function index(Request $request)
    {
        $permohonan = Permohonan::where('var_type', $this->permohonanType)
            ->with('templateDocs')
            ->latest()
            ->paginate($request->query('per_page', 10));
        return PermohonanResource::collection($permohonan);
    }

    public function store(FormRequest $request)
    {
        $validated = $request->validated();
        $validated['var_type'] = $this->permohonanType;

        // Generate nomor permohonan & pengesahan
        $tahun = now()->year;
        $last = (Permohonan::latest()->first()->id ?? 0) + 1;
        $preFixNomorPermohonan = KeyStorage::where('var_key', 'preFixNomorPermohonan')->first()->var_value;
        $postFixNomorPermohonan = KeyStorage::where('var_key', 'postFixNomorPermohonan')->first()->var_value;
        $preFixNomorSurat = KeyStorage::where('var_key', 'preFixNomorSurat')->first()->var_value;
        $postFixNomorSurat = KeyStorage::where('var_key', 'postFixNomorSurat')->first()->var_value;

        $validated['var_nomor_permohonan'] = "{$preFixNomorPermohonan}{$last}{$postFixNomorPermohonan}{$tahun}";
        $validated['var_nomor_pengesahan'] = "{$preFixNomorSurat}{$last}{$postFixNomorSurat}{$tahun}";

        // Buat permohonan DULUAN, tanpa data attachment
        $permohonan = Permohonan::create($validated);

        // Proses attachment
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
            $tempPath = $validated[$field] ?? null;  // e.g., "tmp/randomname.pdf"
            if ($tempPath && Storage::disk('public')->exists($tempPath)) {
                $fileName = basename($tempPath);
                $permanentPath = "permohonan/{$permohonan->id}/{$field}/{$fileName}";
                Storage::disk('public')->move($tempPath, $permanentPath);
                $finalAttachmentPaths[$field] = $permanentPath;
            }
        }

        // Update record permohonan dengan path attachment yang permanen
        if (!empty($finalAttachmentPaths)) {
            $permohonan->update($finalAttachmentPaths);
        }

        // Proses relasi template docs
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

        return (new PermohonanResource($permohonan->load('templateDocs')))
            ->additional(['message' => 'Permohonan berhasil dibuat.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Permohonan $permohonan)
    {
        if ($permohonan->var_type !== $this->permohonanType) {
            abort(404, 'Data tidak ditemukan.');
        }
        return new PermohonanResource($permohonan->load('templateDocs'));
    }

    public function update(FormRequest $request, Permohonan $permohonan)
    {
        if ($permohonan->var_type !== $this->permohonanType) {
            abort(404, 'Data tidak ditemukan.');
        }
        $validated = $request->validated();

        // Proses attachment
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
            $tempPath = $validated[$field] ?? null;  // e.g., "tmp/randomname.pdf"
            if ($tempPath && Storage::disk('public')->exists($tempPath)) {
                // Delete old file if exists
                if ($permohonan->$field && Storage::disk('public')->exists($permohonan->$field)) {
                    Storage::disk('public')->delete($permohonan->$field);
                }

                $fileName = basename($tempPath);
                $permanentPath = "permohonan/{$permohonan->id}/{$field}/{$fileName}";
                Storage::disk('public')->move($tempPath, $permanentPath);
                $finalAttachmentPaths[$field] = $permanentPath;
            }
        }

        // Update record permohonan dengan data validated dan path attachment yang permanen
        $updateData = array_merge($validated, $finalAttachmentPaths);
        $permohonan->update($updateData);

        // Proses relasi template docs
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

        return (new PermohonanResource($permohonan->load('templateDocs')))
            ->additional(['message' => 'Permohonan berhasil diperbarui.']);
    }

    public function destroy(Permohonan $permohonan)
    {
        if ($permohonan->var_type !== $this->permohonanType) {
            abort(404, 'Data tidak ditemukan.');
        }
        $permohonan->delete();
        return response()->json(['message' => 'Permohonan berhasil dihapus.'], Response::HTTP_OK);
    }

    /**
     * Generate document(s) for the given permohonan.
     *
     * @param  Permohonan  $permohonan
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDocuments(Permohonan $permohonan)
    {
        if ($permohonan->var_type !== $this->permohonanType) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $templates = $permohonan->templateDocs;

        // 1. SIAPKAN DATA SIMPEL
        $replacementData = $permohonan->getAttributes();

        $replacementData['var_provinsi']        = $permohonan->nama_provinsi;
        $replacementData['var_kabupaten']       = $permohonan->nama_kabupaten;
        $replacementData['var_kecamatan']       = $permohonan->nama_kecamatan;
        $replacementData['var_kelurahan']       = $permohonan->nama_kelurahan;
        $replacementData['var_kecamatan_usaha'] = $permohonan->nama_kecamatan_usaha;
        $replacementData['var_kelurahan_usaha'] = $permohonan->nama_kelurahan_usaha;
        unset($replacementData['json_geometry']);

        // 2. SIAPKAN DATA TABEL KOORDINAT
        $koordinatList = $permohonan->koordinat ?? [];
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

        $generatedFiles = [];
        foreach ($templates as $template) {
            try {
                $templatePath = Storage::disk('public')->path($template->var_file_path);

                if (!file_exists($templatePath)) {
                    Log::error("File template tidak ditemukan: {$templatePath}");
                    continue;
                }

                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

                // 3. PROSES DATA SIMPEL (PAKAI setValues)
                $templateProcessor->setValues($replacementData);

                // 4. PROSES DATA TABEL (PAKAI cloneRowAndSetValues)
                $templateProcessor->cloneRowAndSetValues('koor_no', $tableValues);

                // 5. SIMPAN FILE
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

                $generatedFiles[] = [
                    'template_id' => $template->id,
                    'file_name'   => $newFileName,
                    'file_url'    => asset('storage/' . $newFilePath),
                ];
            } catch (Exception $e) {
                Log::error('Gagal generate dokumen: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Gagal generate: ' . $template->var_nama . ' - ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'message' => 'Semua dokumen berhasil di-generate!',
            'generated_files' => $generatedFiles,
        ], 200);
    }
}
