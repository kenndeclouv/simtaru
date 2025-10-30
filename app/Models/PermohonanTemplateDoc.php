<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PermohonanTemplateDoc extends Model
{
    protected $guarded = ["id"];
    protected $table = "permohonans_template_docs";
    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class, "fk_permohonan_id");
    }
    public function templateDocs()
    {
        return $this->belongsTo(TemplateDocs::class, "fk_template_docs_id");
    }

    public function getVarGeneratedFilePathAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return asset('storage/' . $value);
    }
}
