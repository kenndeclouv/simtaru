<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TemplateDocResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_dokumen' => $this->var_nama,
            'path_template' => $this->var_file_path ? Storage::disk('public')->path($this->var_file_path) : null,

            'path_hasil_generate' => $this->whenPivotLoaded('permohonans_template_docs', function () {
                return $this->pivot->var_generated_file_path
                    ? Storage::disk('public')->path($this->pivot->var_generated_file_path)
                    : null;
            }),

            'placeholders' => $this->whenLoaded('placeholders', function () {
                return $this->placeholders->pluck('var_key');
            }),
        ];
    }
}
