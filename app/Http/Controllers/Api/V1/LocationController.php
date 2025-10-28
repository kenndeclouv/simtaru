<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function provinces()
    {
        $provinces = Province::select('id', 'name as nama')->get();

        return response()->json([
            'success' => true,
            'data' => $provinces,
            'message' => $provinces->isEmpty() ? 'Data tidak ditemukan' : 'Data ditemukan'
        ]);
    }

    public function regencies($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)
            ->select('id', 'name as nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $regencies,
            'message' => $regencies->isEmpty() ? 'Data tidak ditemukan' : 'Data ditemukan'
        ]);
    }

    public function districts($regencyId)
    {
        $districts = District::where('regency_id', $regencyId)
            ->select('id', 'name as nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $districts,
            'message' => $districts->isEmpty() ? 'Data tidak ditemukan' : 'Data ditemukan'
        ]);
    }

    public function villages($districtId)
    {
        $villages = Village::where('district_id', $districtId)
            ->select('id', 'name as nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $villages,
            'message' => $villages->isEmpty() ? 'Data tidak ditemukan' : 'Data ditemukan'
        ]);
    }
}
