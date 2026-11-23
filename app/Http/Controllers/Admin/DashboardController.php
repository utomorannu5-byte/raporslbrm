<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the administrator dashboard.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard');
    }
}
