<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Http\Resources\PermohonanResource;
use App\Models\KeyStorage;
use App\Models\Permohonan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

trait ManagesPermohonan
{
    public function index()
    {
        $permohonan = Permohonan::where('var_type', $this->permohonanType)
            ->with('templateDocs')
            ->latest()
            ->paginate(10);
        return PermohonanResource::collection($permohonan);
    }

    public function store(FormRequest $request)
    {
        $validated = $request->validated();
        $validated['var_type'] = $this->permohonanType;

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
        $permohonan->update($validated);

        if (isset($validated['pilihan_redaksi_ids'])) {
            $permohonan->templateDocs()->sync($validated['pilihan_redaksi_ids']);
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
}
