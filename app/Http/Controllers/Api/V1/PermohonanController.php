<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermohonanResource;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PermohonanController extends Controller
{
    public function tteQueue(Request $request)
    {
        $type = $request->query('type');

        $query = Permohonan::where('enum_status', 'request_tte')
                 ->with([
                    'permohonanTemplateDocs.templateDocs.placeholders',
                    'user',
                    'userRequestTteBy',
                    'province',
                    'regency',
                    'district',
                    'village',
                    'districtUsaha',
                    'villageUsaha'
                 ]);

        $query->when($type, function ($q, $type) {
            return $q->where('var_type', $type);
        });

        $permohonans = $query->latest()->paginate($request->query('per_page', 10));

        return PermohonanResource::collection($permohonans);
    }

    public function timeline(Request $request, Permohonan $permohonan)
    {
        $this->authorize('view', $permohonan);

        $activities = $permohonan->activities()
            ->with('causer')
            ->latest()
            ->get();

        return ActivityLogResource::collection($activities);
    }

    public function updateSignedDocument(Request $request, Permohonan $permohonan)
    {
        $validator = Validator::make($request->all(), [
            'generated_doc_id' => 'required|exists:permohonans_template_docs,id',
            'var_generated_file_path' => 'required|string',
            'var_penandatangan' => 'nullable|string',
            'approved_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $docPivot = $permohonan->permohonanTemplateDocs()
            ->where('id', $request->generated_doc_id)
            ->first();

        if (!$docPivot) {
            return response()->json(['message' => 'Dokumen tidak ditemukan untuk permohonan ini.'], 404);
        }

        try {
            $inputValue = $request->input('var_generated_file_path');
            $finalPath = null;

            if (str_starts_with($inputValue, 'http')) {
                $finalPath = $inputValue;

            } else {
                if (Storage::disk('public')->exists($inputValue)) {
                    if ($docPivot->var_generated_file_path &&
                        !str_starts_with($docPivot->var_generated_file_path, 'http') &&
                        Storage::disk('public')->exists($docPivot->var_generated_file_path)) {
                        Storage::disk('public')->delete($docPivot->var_generated_file_path);
                    }

                    $fileName = 'TTE_' . time() . '_' . basename($inputValue);
                    $permanentPath = "permohonan/{$permohonan->id}/tte_documents/{$fileName}";

                    Storage::disk('public')->move($inputValue, $permanentPath);
                    $finalPath = $permanentPath;
                } else {
                    return response()->json(['message' => 'File tidak ditemukan di storage sementara.'], 404);
                }
            }

            $docPivot->update([
                'var_generated_file_path' => $finalPath,
            ]);

            $permohonan->update([
                'enum_status' => 'approved',
                'approved_date' => $request->signed_at ?? now(),
                'var_penandatangan' => $request->var_penandatangan ?? "Sistem Simpadu",
            ]);

            activity()
               ->on($permohonan)
               ->byAnonymous()
               ->withProperties([
                   'signer' => $request->var_penandatangan ?? 'Sistem Simpadu',
                   'file_type' => str_starts_with($inputValue, 'http') ? 'external_url' : 'uploaded_file'
               ])
               ->log('Dokumen telah ditandatangani elektronik (TTE) dan diterima dari SIMPADU.');

            $responsePath = str_starts_with($finalPath, 'http') ? $finalPath : asset('storage/' . $finalPath);

            return response()->json([
                'message' => 'Dokumen TTE berhasil disimpan dan status permohonan diperbarui.',
                'path' => $responsePath,
            ]);

        } catch (Exception $e) {
            Log::error('API TTE Callback Error (ID: ' . $permohonan->id . '): ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses dokumen TTE. Hubungi administrator SIMTARU.'], 500);
        }
    }

    public function updateSkDocument(Request $request, Permohonan $permohonan)
    {
        
        $validator = Validator::make($request->all(), [
            'sk_file' => 'required|string', 
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal_terbit' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $inputValue = $request->input('sk_file');
            $finalPath = null;

            
            if (str_starts_with($inputValue, 'http')) {
                
                $finalPath = $inputValue;
            } else {
                
                if (Storage::disk('public')->exists($inputValue)) {
                    
                    if ($permohonan->var_sk_attachment &&
                        !str_starts_with($permohonan->var_sk_attachment, 'http') &&
                        Storage::disk('public')->exists($permohonan->var_sk_attachment)) {
                        Storage::disk('public')->delete($permohonan->var_sk_attachment);
                    }

                    
                    $fileName = 'SK_' . time() . '_' . basename($inputValue);
                    $permanentPath = "permohonan/{$permohonan->id}/sk_documents/{$fileName}";
                    Storage::disk('public')->move($inputValue, $permanentPath);
                    $finalPath = $permanentPath;
                } else {
                    return response()->json(['message' => 'File SK tidak ditemukan di storage sementara.'], 404);
                }
            }

            
            $permohonan->update([
                'var_sk_attachment' => $finalPath,
                'var_nomor_sk' => $request->nomor_sk,
                'date_sk_terbit' => $request->tanggal_terbit ?? now(),
                
                'enum_status' => 'approved',
            ]);

            
            activity()
               ->on($permohonan)
               ->byAnonymous()
               ->withProperties([
                   'nomor_sk' => $request->nomor_sk ?? '-',
                   'file_type' => str_starts_with($inputValue, 'http') ? 'external_url' : 'uploaded_file'
               ])
               ->log('Menerima Dokumen SK Final dari SIMPADU.');

            $responsePath = str_starts_with($finalPath, 'http') ? $finalPath : asset('storage/' . $finalPath);

            return response()->json([
                'message' => 'Dokumen SK berhasil disimpan.',
                'path' => $responsePath,
            ]);

        } catch (\Exception $e) {
            Log::error('API SK Callback Error (ID: ' . $permohonan->id . '): ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses dokumen SK.'], 500);
        }
    }
}
