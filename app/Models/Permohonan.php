<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    protected $guarded = ['id'];

    public function templateDocs()
    {
        return $this->belongsToMany(TemplateDocs::class, "permohonans_template_docs", "fk_permohonan_id", "fk_template_docs_id");
    }
}
