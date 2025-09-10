<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocs extends Model
{
    protected $guarded = ["id"];
    public function placeholders()
    {
        return $this->hasMany(TemplateDocsPlaceholder::class, 'fk_template_docs_id');
    }

    public function permohonans()
    {
        return $this->belongsToMany(Permohonan::class, 'permohonans_template_docs', 'fk_template_docs_id', 'fk_permohonan_id');
    }
}
