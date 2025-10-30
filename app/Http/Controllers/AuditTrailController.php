<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditTrailController extends Controller
{
    public function index()
    {
        $activities = Activity::latest()->paginate(20);
        return view('audit.index', compact('activities'));
    }
}
