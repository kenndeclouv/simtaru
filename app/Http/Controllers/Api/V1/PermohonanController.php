<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermohonanRequest;
use App\Http\Resources\PermohonanResource;
use App\Models\KeyStorage;
use App\Models\Permohonan;
use App\Models\TemplateDocs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class PermohonanController extends Controller
{
    public function __construct()
    {
        // Menggunakan auth:sanctum untuk proteksi API
        $this->middleware('auth:sanctum');

        // Middleware permission tetap berlaku untuk API
        $this->middleware('can:view permohonan')->only(['index', 'show']);
        $this->middleware('can:create permohonan')->only(['store']);
        $this->middleware('can:edit permohonan')->only(['update', 'status', 'generateDocuments']);
        $this->middleware('can:delete permohonan')->only(['destroy']);
    }

    /**
     * Menampilkan daftar permohonan dengan pagination.
     * Yajra DataTables diganti dengan pagination standar API.
     */
    public function index()
    {
        $permohonan = Permohonan::with('templateDocs')->latest()->paginate(10);

        return PermohonanResource::collection($permohonan);
    }

    /**
     * Menyimpan permohonan baru.
     */
    public function store(PermohonanRequest $request)
    {
        $validated = $request->validated();

        $tahun = now()->year;
        $last = (Permohonan::latest()->first()->id ?? 0) + 1;
        $preFixNomorPermohonan = KeyStorage::where('var_key', 'preFixNomorPermohonan')->first()->var_value;
        $postFixNomorPermohonan = KeyStorage::where('var_key', 'postFixNomorPermohonan')->first()->var_value;
        $preFixNomorSurat = KeyStorage::where('var_key', 'preFixNomorSurat')->first()->var_value;
        $postFixNomorSurat = KeyStorage::where('var_key', 'postFixNomorSurat')->first()->var_value;

        $validated['var_nomor_permohonan'] = "{$preFixNomorPermohonan}{$last}{$postFixNomorPermohonan}{$tahun}";
        $validated['var_nomor_pengesahan'] = "{$preFixNomorSurat}{$last}{$postFixNomorSurat}{$tahun}";

        $permohonan = Permohonan::create($validated);

        if (isset($validated['pilihan_redaksi_ids'])) {
            $permohonan->templateDocs()->attach($validated['pilihan_redaksi_ids']);
        }

        // Respon JSON dengan status 201 Created
        return (new PermohonanResource($permohonan->load('templateDocs')))
            ->additional(['message' => 'Permohonan berhasil dibuat.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Menampilkan detail satu permohonan.
     */
    public function show(Permohonan $permohonan)
    {
        // Mengembalikan satu data permohonan yang sudah ditransformasi
        return new PermohonanResource($permohonan->load('templateDocs'));
    }

    /**
     * Memperbarui data permohonan.
     */
    public function update(PermohonanRequest $request, Permohonan $permohonan)
    {
        $validated = $request->validated();
        $permohonan->update($validated);

        if (isset($validated['pilihan_redaksi_ids'])) {
            $permohonan->templateDocs()->sync($validated['pilihan_redaksi_ids']);
        }

        return (new PermohonanResource($permohonan->load('templateDocs')))
            ->additional(['message' => 'Permohonan berhasil diperbarui.']);
    }

    /**
     * Menghapus data permohonan.
     */
    public function destroy(Permohonan $permohonan)
    {
        $permohonan->delete();

        return response()->json(['message' => 'Permohonan berhasil dihapus.'], Response::HTTP_OK);
    }

    /**
     * Mengubah status permohonan dan upload lampiran.
     */
    public function status(Request $request, Permohonan $permohonan)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'catatan' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,png|max:2048', // Contoh validasi file
        ]);

        $lampiranName = $permohonan->var_lampiran;
        if ($request->hasFile('lampiran')) {
            // Hapus file lama jika ada
            if ($lampiranName && Storage::exists('uploads/' . $lampiranName)) {
                Storage::delete('uploads/' . $lampiranName);
            }
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

        return (new PermohonanResource($permohonan->fresh()))
            ->additional(['message' => 'Status permohonan berhasil diubah.']);
    }

    /**
     * Men-generate dokumen dari template.
     */
    public function generateDocuments(Permohonan $permohonan)
    {
        $generatedFiles = [];
        $templates = $permohonan->templateDocs;

        $replacementData = $permohonan->toArray();
        // Asumsi helper ini ada
        $replacementData['var_provinsi'] = $permohonan->nama_provinsi;
        $replacementData['var_kabupaten'] = $permohonan->nama_kabupaten;
        // ... dan seterusnya

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

                $generatedFiles[] = Storage::disk('public')->path($newFilePath);
            } catch (Exception $e) {
                Log::error("Gagal generate dokumen: " . $e->getMessage());
                return response()->json([
                    'message' => 'Terjadi kesalahan saat generate dokumen.',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return response()->json([
            'message' => 'Semua dokumen berhasil di-generate!',
            'generated_files' => $generatedFiles
        ]);
    }
}
