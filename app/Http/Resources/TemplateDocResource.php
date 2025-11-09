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
        $template = $this->templateDocs;

        if (!$template) {
            return [];
        }

        return [
            'template_id' => $template->id,
            'id' => $this->id,
            'nama_dokumen' => $template->var_nama,
            'path_template' => $template->var_file_path
                ? asset('storage/' . ltrim($template->var_file_path, '/'))
                : null,

            'path_hasil_generate' => $this->var_generated_file_path
        ];
    }
}
