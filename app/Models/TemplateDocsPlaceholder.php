<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocsPlaceholder extends Model
{
    protected $guarded = ["id"];

    protected $table = 'template_docs_placeholders';

    public function templateDocs()
    {
        return $this->belongsTo(TemplateDocs::class, 'fk_template_docs_id');
    }
}
