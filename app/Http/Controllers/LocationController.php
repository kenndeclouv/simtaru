<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function provinces()
    {
        return Province::select('id', 'name as nama')->get();
    }

    public function regencies($provinceId)
    {
        return Regency::where('province_id', $provinceId)
            ->select('id', 'name as nama')
            ->get();
    }

    public function districts($regencyId)
    {
        return District::where('regency_id', $regencyId)
            ->select('id', 'name as nama')
            ->get();
    }

    public function villages($districtId)
    {
        return Village::where('district_id', $districtId)
            ->select('id', 'name as nama')
            ->get();
    }
}
