<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
