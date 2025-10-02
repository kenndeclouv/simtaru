<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ManagesPermohonan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\KkprRequest;
use App\Models\Permohonan;

class KkprController extends Controller
{
    use ManagesPermohonan {
        store as handleStore;
        update as handleUpdate;
        show as handleShow;
        destroy as handleDestroy;
    }

    protected $permohonanType = 'kkpr';

    public function store(KkprRequest $request)
    {
        return $this->handleStore($request);
    }

    public function update(KkprRequest $request, Permohonan $permohonan)
    {
        return $this->handleUpdate($request, $permohonan);
    }

    public function show(Permohonan $permohonan)
    {
        return $this->handleShow($permohonan);
    }

    public function destroy(Permohonan $permohonan)
    {
        return $this->handleDestroy($permohonan);
    }
}
