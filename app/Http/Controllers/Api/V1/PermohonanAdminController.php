<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermohonanResource;
use App\Models\Permohonan;
use Illuminate\Http\Request;
class PermohonanAdminController extends Controller
{
    public function tteQueue(Request $request)
    {
        $type = $request->query('type');

        $query = Permohonan::where('enum_status', 'request_tte')
                 ->with('permohonanTemplateDocs.templateDocs');

        $query->when($type, function ($q, $type) {
            return $q->where('var_type', $type);
        });

        $permohonans = $query->latest()->paginate($request->query('per_page', 10));

        return PermohonanResource::collection($permohonans);
    }
}
