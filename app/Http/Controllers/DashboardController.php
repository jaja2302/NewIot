<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $time = Carbon::now('Asia/Jakarta');

        // Get user's location from the request

        return view('pages.dashboard.dashboard', compact('time'));
    }
}
