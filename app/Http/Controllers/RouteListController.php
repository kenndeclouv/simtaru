<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class RouteListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view route-list')->only(['index']);
    }
    public function index()
    {
        $routes = Route::getRoutes();
        return view('route_list.index', compact('routes'));
    }
}
