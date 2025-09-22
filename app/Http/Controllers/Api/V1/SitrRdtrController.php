<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Concerns\ManagesPermohonan;
use App\Http\Requests\Api\SitrRdtrRequest;
use App\Models\Permohonan;

class SitrRdtrController extends Controller
{
    use ManagesPermohonan {
        store as handleStore;
        update as handleUpdate;
    }

    protected $permohonanType = 'sitr/rdtr';

    public function store(SitrRdtrRequest $request)
    {
        return $this->handleStore($request);
    }

    public function update(SitrRdtrRequest $request, Permohonan $permohonan)
    {
        return $this->handleUpdate($request, $permohonan);
    }
}
